# Helpers shared by the ZED server scripts (sourced, not run).
# Ubuntu 22.04 / 24.04, bash. The server may host other websites: these
# scripts only add the ZED site and never change other nginx sites, other
# PHP-FPM pools or the firewall.

set -Eeuo pipefail

ZED_HOST="${ZED_HOST:-zedcms.umon.in}"
ZED_ROOT="${ZED_ROOT:-/var/www/zedcms}"
ZED_USER="${ZED_USER:-zedcms}"            # Linux user the website's PHP runs as
PHP_VERSION="${PHP_VERSION:-8.4}"         # locked libraries need PHP >= 8.4.1
ACME_ROOT="${ACME_ROOT:-/var/www/letsencrypt}"
LOG_DIR="/var/log/zed-deploy"

SHARED="$ZED_ROOT/shared"
RELEASES="$ZED_ROOT/releases"
CURRENT="$ZED_ROOT/current"
ENV_FILE="$SHARED/.env"
MEDIA_DIRS=(uploads slider photogallery)
PHP_BIN="/usr/bin/php${PHP_VERSION}"      # explicit: other sites may use another default php
PHP_SOCK="/run/php/php${PHP_VERSION}-fpm-${ZED_USER}.sock"
NGINX_SITE="/etc/nginx/sites-available/${ZED_USER}.conf"
CERT_DIR="/etc/letsencrypt/live/$ZED_HOST"

step() { printf '\n\033[1;36m==> %s\033[0m\n' "$*"; }
ok()   { printf '    \033[32m%s\033[0m\n' "$*"; }
warn() { printf '    \033[33mWARNING: %s\033[0m\n' "$*" >&2; }
die()  { printf '\n\033[1;31mERROR: %s\033[0m\n' "$*" >&2; exit 1; }

require_root() { [[ $EUID -eq 0 ]] || die "Run with sudo, e.g.: sudo bash $0 $*"; }

# Copy everything printed to a log file under /var/log/zed-deploy.
start_log() {
    mkdir -p "$LOG_DIR"
    LOG_FILE="$LOG_DIR/$1-$(date +%Y%m%d-%H%M%S).log"
    exec > >(tee -a "$LOG_FILE") 2>&1
    echo "Log file: $LOG_FILE"
}

random_secret() {  # letters and digits, safe in .env, SQL and URLs
    python3 -c 'import secrets, string; a = string.ascii_letters + string.digits; print("".join(secrets.choice(a) for _ in range(int("'"${1:-32}"'"))))'
}

laravel_key() { echo "base64:$(openssl rand -base64 32)"; }

# --- .env files ---------------------------------------------------------------
env_get() {  # env_get FILE KEY  -> value without quotes ('' when missing)
    [[ -f $1 ]] || return 0
    K="$2" awk -F= '
        $0 ~ "^[[:space:]]*" ENVIRON["K"] "[[:space:]]*=" {
            sub(/^[^=]*=[[:space:]]*/, ""); sub(/[[:space:]]+$/, "")
            if ($0 ~ /^".*"$/ || $0 ~ /^\x27.*\x27$/) $0 = substr($0, 2, length($0) - 2)
            print; exit
        }' "$1"
}

env_set() {  # env_set FILE KEY VALUE  (replaces the first KEY= line, also "# KEY=", or appends)
    local file=$1 key=$2 value=$3 tmp
    if [[ $value =~ [[:space:]#\"\'\\\$=] ]]; then
        if [[ $value != *"'"* ]]; then value="'$value'"
        else value="\"${value//\\/\\\\}\""; value="${value//\$/\\\$}"; fi
    fi
    tmp=$(mktemp)
    K="$key" V="$value" awk '
        !done && $0 ~ "^[[:space:]]*#?[[:space:]]*" ENVIRON["K"] "[[:space:]]*=" { print ENVIRON["K"] "=" ENVIRON["V"]; done = 1; next }
        { print }
        END { if (!done) print ENVIRON["K"] "=" ENVIRON["V"] }' "$file" > "$tmp"
    cat "$tmp" > "$file"   # keeps the file's owner and permissions
    rm -f "$tmp"
}

# The website's settings file, kept outside the releases: $ZED_ROOT/shared/.env
init_shared_env() {  # init_shared_env TEMPLATE
    mkdir -p "$SHARED"
    if [[ ! -f $ENV_FILE ]]; then
        [[ -f $1 ]] || die "Template $1 not found."
        cp "$1" "$ENV_FILE"
        env_set "$ENV_FILE" APP_ENV staging
        env_set "$ENV_FILE" APP_DEBUG false
        env_set "$ENV_FILE" APP_URL "http://$ZED_HOST"
        env_set "$ENV_FILE" APP_KEY "$(laravel_key)"
        env_set "$ENV_FILE" LOG_LEVEL warning
        env_set "$ENV_FILE" SESSION_SECURE_COOKIE false   # true once HTTPS is on (enable-https.sh)
        env_set "$ENV_FILE" MAIL_MAILER log               # staging: e-mails go to storage/logs/laravel.log
        env_set "$ENV_FILE" ZED_STORAGE_ROOT ''
        ok "created $ENV_FILE"
    fi
    chown root:"$ZED_USER" "$ENV_FILE" 2>/dev/null || true
    chmod 640 "$ENV_FILE"
}

# --- nginx ----------------------------------------------------------------------
render_template() {
    sed -e "s|__HOST__|$ZED_HOST|g" -e "s|__ROOT__|$ZED_ROOT|g" -e "s|__SOCK__|$PHP_SOCK|g" \
        -e "s|__ACME__|$ACME_ROOT|g" -e "s|__CERT__|$CERT_DIR|g" "$1"
}

# Writes the ZED server block: http only, or http -> https redirect plus https
# once the Let's Encrypt certificate exists. Other sites are not touched.
write_nginx_site() {
    local template="$SCRIPT_DIR/nginx-http.conf.template"
    [[ -f $CERT_DIR/fullchain.pem ]] && template="$SCRIPT_DIR/nginx-https.conf.template"
    [[ -f $template ]] || die "nginx template $template not found."
    local backup=""
    [[ -f $NGINX_SITE ]] && { backup=$(mktemp); cp "$NGINX_SITE" "$backup"; }
    local app_conf="$SHARED/nginx-app.conf" app_backup=""
    [[ -f $app_conf ]] && { app_backup=$(mktemp); cp "$app_conf" "$app_backup"; }
    render_template "$SCRIPT_DIR/nginx-app.conf.template" > "$app_conf"
    render_template "$template" > "$NGINX_SITE"
    ln -sfn "$NGINX_SITE" "/etc/nginx/sites-enabled/${ZED_USER}.conf"
    if ! nginx -t 2>&1; then
        if [[ -n $backup ]]; then cp "$backup" "$NGINX_SITE"; else rm -f "$NGINX_SITE" "/etc/nginx/sites-enabled/${ZED_USER}.conf"; fi
        if [[ -n $app_backup ]]; then cp "$app_backup" "$app_conf"; fi
        die "nginx rejected the new configuration; the previous one was restored."
    fi
    if [[ -n $backup ]]; then rm -f "$backup"; fi
    if [[ -n $app_backup ]]; then rm -f "$app_backup"; fi
    systemctl reload nginx
    ok "nginx: $NGINX_SITE ($(basename "$template" .conf.template))"
}

artisan() {  # artisan RELEASE ARGS...  (runs as the website user)
    local dir=$1; shift
    (cd "$dir" && sudo -u "$ZED_USER" -H "$PHP_BIN" artisan "$@")
}
