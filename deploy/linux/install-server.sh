#!/usr/bin/env bash
# STEP 2 - on the Ubuntu server, once (safe to run again).
# Installs what the ZED website needs, next to any websites already on the server:
#   nginx (if missing), PHP 8.4-FPM (ppa:ondrej/php) with a separate pool for ZED,
#   Composer, MySQL (if no MySQL/MariaDB is installed yet), certbot (free SSL).
# It does not change other nginx sites, other PHP pools or the firewall.
#
#   sudo bash install-server.sh
# Usually run for you by Publish-ZedRelease.ps1 -Setup from the Windows PC.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/zed-common.sh"
require_root
start_log install-server

. /etc/os-release
[[ ${ID:-} == ubuntu ]] || die "This script is for Ubuntu (found ${PRETTY_NAME:-unknown})."
case "${VERSION_ID:-}" in 22.04|24.04|24.10|25.04|25.10|26.04) ;; *) warn "Tested on Ubuntu 22.04 and 24.04; found $VERSION_ID." ;; esac
ok "$PRETTY_NAME"
export DEBIAN_FRONTEND=noninteractive LC_ALL=C.UTF-8

step 'Base packages'
apt-get update -q
apt-get install -y -q ca-certificates curl unzip python3 software-properties-common openssl
ok 'done'

step 'nginx'
if command -v nginx >/dev/null; then ok "already installed ($(nginx -v 2>&1))"
else apt-get install -y -q nginx; systemctl enable --now nginx; ok 'installed'; fi

step "PHP $PHP_VERSION"
# (output captured first: with pipefail, "cmd | grep -q" can fail when grep stops reading early)
if ! grep -q 'Candidate: [0-9]' <<<"$(apt-cache policy "php${PHP_VERSION}-fpm" 2>/dev/null || true)"; then
    add-apt-repository -y ppa:ondrej/php
    apt-get update -q
fi
apt-get install -y -q "php${PHP_VERSION}-fpm" "php${PHP_VERSION}-cli" "php${PHP_VERSION}-mysql" "php${PHP_VERSION}-mbstring" \
    "php${PHP_VERSION}-xml" "php${PHP_VERSION}-curl" "php${PHP_VERSION}-gd" "php${PHP_VERSION}-intl" "php${PHP_VERSION}-zip" \
    "php${PHP_VERSION}-bcmath" "php${PHP_VERSION}-opcache"
modules=$("$PHP_BIN" -m)
missing=""
for m in curl fileinfo gd intl mbstring openssl pdo_mysql zip sodium exif; do
    grep -qix "$m" <<<"$modules" || missing+=" $m"
done
[[ -z $missing ]] || die "PHP extensions missing:$missing"
ok "$("$PHP_BIN" -r 'echo "PHP ", PHP_VERSION;')"

step "Website user and PHP-FPM pool ($ZED_USER)"
if ! id "$ZED_USER" >/dev/null 2>&1; then
    useradd --system --home-dir "$ZED_ROOT" --no-create-home --shell /usr/sbin/nologin "$ZED_USER"
    ok "created system user $ZED_USER"
fi
mkdir -p "$RELEASES" "$SHARED" "$ACME_ROOT"
for d in app/public app/private framework/cache/data framework/sessions framework/views logs; do mkdir -p "$SHARED/storage/$d"; done
for d in "${MEDIA_DIRS[@]}"; do mkdir -p "$SHARED/media/$d"; done
chown -R "$ZED_USER:$ZED_USER" "$SHARED/storage" "$SHARED/media"
chmod 755 "$ZED_ROOT" "$RELEASES" "$SHARED"

cat > "/etc/php/$PHP_VERSION/fpm/pool.d/$ZED_USER.conf" <<EOF
; ZED website PHP-FPM pool (written by deploy/linux/install-server.sh)
[$ZED_USER]
user = $ZED_USER
group = $ZED_USER
listen = $PHP_SOCK
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 12
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 4
pm.max_requests = 1000
request_terminate_timeout = 120s
; settings for this website only (other sites keep their own)
php_admin_value[upload_max_filesize] = 24M
php_admin_value[post_max_size] = 32M
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 60
php_admin_value[date.timezone] = Asia/Kolkata
php_admin_flag[expose_php] = off
php_admin_flag[display_errors] = off
php_admin_flag[log_errors] = on
php_admin_value[error_log] = $SHARED/storage/logs/php-errors.log
php_admin_value[open_basedir] = $ZED_ROOT/:/tmp/:/usr/share/php/:/etc/ssl/
EOF
"/usr/sbin/php-fpm$PHP_VERSION" -t 2>&1 | tail -1
systemctl enable "php${PHP_VERSION}-fpm" >/dev/null
systemctl restart "php${PHP_VERSION}-fpm"
ok "pool $ZED_USER listening on $PHP_SOCK"

step 'Composer'
if [[ ! -x /usr/local/bin/composer ]]; then
    need_https https://getcomposer.org/installer 'Composer download'
    tmp=$(mktemp -d)
    expected=$(fetch https://composer.github.io/installer.sig)
    fetch https://getcomposer.org/installer -o "$tmp/composer-setup.php"
    actual=$("$PHP_BIN" -r "echo hash_file('sha384', '$tmp/composer-setup.php');")
    [[ $expected == "$actual" ]] || die 'Composer installer checksum mismatch.'
    timeout 300 "$PHP_BIN" "$tmp/composer-setup.php" --quiet --install-dir=/usr/local/bin --filename=composer \
        || die 'Composer setup did not finish in 5 minutes (it downloads from getcomposer.org over HTTPS).'
    rm -rf "$tmp"
fi
# the website's PHP libraries come from these sites (deploy.sh), the dashboard figures from the ZED portal
need_https https://repo.packagist.org/packages.json 'PHP libraries (Packagist)'
need_https https://codeload.github.com 'PHP libraries (GitHub downloads)'
ok "$("$PHP_BIN" /usr/local/bin/composer --version 2>/dev/null | head -1)"

step 'MySQL'
if command -v mysqld >/dev/null || command -v mariadbd >/dev/null || grep -qE '^(mysql|mariadb)\.service' <<<"$(systemctl list-unit-files 2>/dev/null || true)"; then
    ok "already installed ($(mysql --version 2>/dev/null || echo 'version unknown')); the ZED database will be added to it"
else
    apt-get install -y -q mysql-server
    cat > /etc/mysql/mysql.conf.d/zed.cnf <<'EOF'
[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
max_allowed_packet = 64M
EOF
    systemctl enable --now mysql
    systemctl restart mysql
    ok "installed ($(mysql --version)); listens on 127.0.0.1 only"
fi

step "certbot (free SSL certificates from Let's Encrypt)"
if ! command -v certbot >/dev/null; then apt-get install -y -q certbot; fi
mkdir -p /etc/letsencrypt/renewal-hooks/deploy
cat > /etc/letsencrypt/renewal-hooks/deploy/zed-reload-nginx.sh <<'EOF'
#!/bin/sh
# reload nginx after a certificate is renewed (written by the ZED install script)
systemctl reload nginx
EOF
chmod 755 /etc/letsencrypt/renewal-hooks/deploy/zed-reload-nginx.sh
ok "$(certbot --version 2>&1); automatic renewal: $(systemctl is-enabled certbot.timer 2>/dev/null || echo 'snap timer')"

echo
printf '\033[1;32mServer software ready.\033[0m Next: init-database.sh\n'
