#!/usr/bin/env bash
# STEP 4 - on the Ubuntu server, for the first install and for every update.
# Installs this release package as the zedcms.umon.in website.
#
#   sudo bash deploy.sh [--media FILE.zip] [--admin-email ADDRESS] [--https-email ADDRESS]
#   sudo bash deploy.sh --rollback
#
#   --media        ZED-media-*.zip (uploads, slider, photogallery): files are added,
#                  newer files already on the server are kept
#   --admin-email  also create (or reset the password of) this CMS superadmin
#   --https-email  also get the free Let's Encrypt SSL certificate if HTTPS is not on yet
#   --rollback     switch back to the previous release
#
# Layout:  /var/www/zedcms/current -> releases/<date-time>   (nginx root: current/public)
#          /var/www/zedcms/shared/.env, storage/, media/      (kept across releases)
# If a step fails before the switch, the website keeps running the previous release.
# Usually run for you by Publish-ZedRelease.ps1 from the Windows PC.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/zed-common.sh"
require_root
PACKAGE="$(dirname "$SCRIPT_DIR")"

MEDIA=""; ADMIN_EMAIL=""; HTTPS_EMAIL=""; ROLLBACK=0; KEEP=3
while [[ $# -gt 0 ]]; do
    case $1 in
        --media) MEDIA=$2; shift 2 ;;
        --admin-email) ADMIN_EMAIL=$2; shift 2 ;;
        --https-email) HTTPS_EMAIL=$2; shift 2 ;;
        --rollback) ROLLBACK=1; shift ;;
        *) die "Unknown option $1" ;;
    esac
done
start_log deploy
id "$ZED_USER" >/dev/null 2>&1 || die "User $ZED_USER not found. Run install-server.sh first."
[[ -x $PHP_BIN ]] || die "$PHP_BIN not found. Run install-server.sh first."

reload_php() { systemctl reload "php${PHP_VERSION}-fpm"; }   # clears OPcache for the new code

check_site() {
    step "Checking the website ($ZED_HOST)"
    local scheme=http port=80 code path
    if [[ $(env_get "$ENV_FILE" APP_URL) == https://* ]]; then scheme=https; port=443; fi
    for path in / /cms/login; do
        code=$(curl -sk -o /dev/null -w '%{http_code}' --max-time 60 --resolve "$ZED_HOST:$port:127.0.0.1" "$scheme://$ZED_HOST$path" || true)
        if [[ $code == 200 ]]; then ok "$path -> HTTP $code"
        else warn "$path -> HTTP $code. See $SHARED/storage/logs/laravel.log and /var/log/nginx/zedcms.error.log"; fi
    done
}

switch_to() {  # atomic: current -> $1
    ln -sfn "$1" "$ZED_ROOT/current.new"
    mv -Tf "$ZED_ROOT/current.new" "$CURRENT"
    reload_php
}

# --- rollback --------------------------------------------------------------------
if (( ROLLBACK )); then
    active=$(basename "$(readlink -f "$CURRENT" || true)")
    # the newest release older than the live one
    previous=$(find "$RELEASES" -mindepth 1 -maxdepth 1 -type d | sort -r | while read -r d; do
        if [[ $(basename "$d") < $active ]]; then echo "$d"; break; fi; done || true)
    [[ -n $previous ]] || die 'No previous release to go back to.'
    step "Switching back to $(basename "$previous")"
    switch_to "$previous"
    artisan "$CURRENT" optimize
    check_site
    printf '\033[1;32mRolled back.\033[0m Database changes are not undone (they are additive).\n'
    exit 0
fi

[[ -f $PACKAGE/app/artisan ]] || die "Run this from the unpacked release package (expected $PACKAGE/app/artisan)."
if [[ -f $PACKAGE/RELEASE.txt ]]; then cat "$PACKAGE/RELEASE.txt"; fi

# --- 1. settings ---------------------------------------------------------------------
step 'Settings (shared/.env)'
init_shared_env "$PACKAGE/app/.env.example"
[[ -n $(env_get "$ENV_FILE" DB_PASSWORD) ]] || die "No database settings in $ENV_FILE. Run init-database.sh first."
[[ -n $(env_get "$ENV_FILE" APP_KEY) ]] || env_set "$ENV_FILE" APP_KEY "$(laravel_key)"
ok "APP_URL = $(env_get "$ENV_FILE" APP_URL)"

# --- 2. copy the release -------------------------------------------------------------
RELEASE="$RELEASES/$(date +%Y%m%d-%H%M%S)"
step "Copying the website to $RELEASE"
LIVE=0
cleanup_failed() {
    if (( ! LIVE )) && [[ -d $RELEASE && $(readlink -f "$CURRENT" 2>/dev/null) != "$RELEASE" ]]; then
        echo 'Deployment stopped. The website still runs the previous release; removing the unfinished copy.'
        rm -rf "$RELEASE"
    fi
}
trap 'cleanup_failed' EXIT
mkdir -p "$RELEASE"
cp -a "$PACKAGE/app/." "$RELEASE/"
mkdir -p "$RELEASE/public/assets"
cp -a "$PACKAGE/assets/." "$RELEASE/public/assets/"
find "$RELEASE/public/assets" -type f \( -iname '*.php' -o -iname '*.phtml' -o -iname '*.phar' \) -delete
if [[ -f $PACKAGE/RELEASE.txt ]]; then cp "$PACKAGE/RELEASE.txt" "$RELEASE/"; fi

# --- 3. shared folders -----------------------------------------------------------------
step 'Linking settings, storage and uploaded files'
for d in app/public app/private framework/cache/data framework/sessions framework/views logs; do mkdir -p "$SHARED/storage/$d"; done
rm -rf "$RELEASE/storage"
ln -s "$SHARED/storage" "$RELEASE/storage"
ln -sfn "$ENV_FILE" "$RELEASE/.env"
for d in "${MEDIA_DIRS[@]}"; do mkdir -p "$SHARED/media/$d"; done
if [[ -n $MEDIA ]]; then
    [[ -f $MEDIA ]] || die "Media package not found: $MEDIA"
    echo "    adding files from $MEDIA"
    # -u: only new files or files newer than the ones on the server
    unzip -u -o -q "$MEDIA" -d "$SHARED/media" -x '*.php' '*.phtml' '*.phar'
fi
for d in "${MEDIA_DIRS[@]}"; do
    rm -rf "${RELEASE:?}/public/$d"
    ln -s "$SHARED/media/$d" "$RELEASE/public/$d"
    ok "$d: $(find "$SHARED/media/$d" -type f | wc -l) files"
done
chown -R "$ZED_USER:$ZED_USER" "$SHARED/storage" "$SHARED/media"

# --- 4. PHP libraries, database changes, caches -----------------------------------------
step 'Installing PHP libraries (composer install)'
(cd "$RELEASE" && COMPOSER_ALLOW_SUPERUSER=1 "$PHP_BIN" /usr/local/bin/composer install \
    --no-dev --optimize-autoloader --no-interaction --no-progress)
chown -R root:root "$RELEASE"
chmod -R u=rwX,go=rX "$RELEASE"
chown -R "$ZED_USER:$ZED_USER" "$RELEASE/bootstrap/cache" "$SHARED/storage"   # composer's artisan call ran as root

step 'Database migrations'
artisan "$RELEASE" migrate --force

step 'Building caches (config, routes, views)'
artisan "$RELEASE" optimize:clear
artisan "$RELEASE" optimize

# --- 5. nginx and go live -----------------------------------------------------------------
step 'nginx'
write_nginx_site

step 'Switching the website to the new release'
switch_to "$RELEASE"
LIVE=1
ok "current -> $RELEASE"

find "$RELEASES" -mindepth 1 -maxdepth 1 -type d | sort -r | tail -n +$((KEEP + 1)) | while read -r old; do
    echo "    removing old release $(basename "$old")"
    rm -rf "$old"      # links to shared/ are removed, never followed
done

if [[ -n $ADMIN_EMAIL ]]; then
    step "CMS superadmin $ADMIN_EMAIL"
    artisan "$CURRENT" zed:cms-admin "$ADMIN_EMAIL"
fi

check_site

if [[ -n $HTTPS_EMAIL && ! -f $CERT_DIR/fullchain.pem ]]; then
    step "Free SSL certificate (Let's Encrypt)"
    if ! bash "$SCRIPT_DIR/enable-https.sh" --email "$HTTPS_EMAIL"; then
        warn 'The website is deployed, but the SSL certificate step failed (see above).'
        warn 'Fix the cause (usually DNS or port 80) and run enable-https.sh --email ADDRESS.'
    fi
fi

url=$(env_get "$ENV_FILE" APP_URL)
echo
printf '\033[1;32mDeployed.\033[0m Website: %s/   CMS: %s/cms\n' "$url" "$url"
[[ $url == https://* ]] || echo "Next: enable-https.sh --email ADDRESS (free Let's Encrypt SSL certificate)."
