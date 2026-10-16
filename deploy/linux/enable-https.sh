#!/usr/bin/env bash
# STEP 5 - on the Ubuntu server, once: FREE SSL certificate from Let's Encrypt.
#
#   sudo bash enable-https.sh --email you@example.com    # get the certificate, switch the site to HTTPS
#   sudo bash enable-https.sh --status                   # expiry date and automatic renewal check
#
# The certificate is free and valid for 90 days. certbot's systemd timer checks
# twice a day and renews it about 30 days before it expires; nginx is reloaded
# automatically afterwards. Nothing to do by hand.
# Requirements: zedcms.umon.in must point to this server (DNS A record) and
# port 80 must be open to the internet (AWS security group), for the first
# certificate and for every renewal. Let's Encrypt no longer e-mails before a
# certificate expires, so run --status now and then.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/zed-common.sh"
require_root

EMAIL=""; STATUS=0; DNS_CHECK=1
while [[ $# -gt 0 ]]; do
    case $1 in
        --email) EMAIL=$2; shift 2 ;;
        --status) STATUS=1; shift ;;
        --skip-dns-check) DNS_CHECK=0; shift ;;
        *) die "Unknown option $1" ;;
    esac
done

show_status() {
    step "SSL certificate for $ZED_HOST"
    if [[ ! -f $CERT_DIR/fullchain.pem ]]; then warn 'No certificate yet (run with --email ADDRESS).'; return; fi
    local end days
    end=$(openssl x509 -enddate -noout -in "$CERT_DIR/fullchain.pem" | cut -d= -f2)
    days=$(( ($(date -d "$end" +%s) - $(date +%s)) / 86400 ))
    ok "issued by: $(openssl x509 -issuer -noout -in "$CERT_DIR/fullchain.pem" | sed 's/^issuer=//')"
    ok "valid until: $end ($days days left)"
    (( days >= 15 )) || warn 'Less than 15 days left: automatic renewal is not working. Run: sudo certbot renew  and read its output.'
    if systemctl is-enabled certbot.timer >/dev/null 2>&1; then
        ok "automatic renewal: certbot.timer, next run $(systemctl show certbot.timer -p NextElapseUSecRealtime --value)"
    elif grep -q 'snap.certbot.renew' <<<"$(systemctl list-timers --all 2>/dev/null || true)"; then
        ok 'automatic renewal: snap.certbot.renew.timer'
    else
        warn 'No certbot renewal timer found. Run: sudo systemctl enable --now certbot.timer'
    fi
}

if (( STATUS )); then show_status; exit 0; fi

start_log https
[[ -L $CURRENT ]] || die 'The website is not deployed yet. Run deploy.sh first.'
command -v certbot >/dev/null || die 'certbot missing. Run install-server.sh first.'

if [[ ! -f $CERT_DIR/fullchain.pem ]]; then
    [[ -n $EMAIL ]] || read -r -p "E-mail address for the Let's Encrypt account: " EMAIL

    step 'Checking DNS'
    server_ip=$(curl -s --max-time 10 https://checkip.amazonaws.com || true)
    dns_ip=$(getent ahostsv4 "$ZED_HOST" | awk 'NR==1 {print $1}' || true)
    if (( DNS_CHECK )) && [[ -n $server_ip && $dns_ip != "$server_ip" ]]; then
        warn "$ZED_HOST points to ${dns_ip:-nothing}, but this server's public address is $server_ip."
        die "Change the DNS A record of $ZED_HOST to $server_ip, wait until it is visible (nslookup $ZED_HOST), then run this again (or add --skip-dns-check if the server sits behind another address)."
    fi
    ok "$ZED_HOST -> ${dns_ip:-?} (this server)"

    step "Requesting the free certificate from Let's Encrypt"
    mkdir -p "$ACME_ROOT"
    write_nginx_site   # http block serving /.well-known/acme-challenge/
    certbot certonly --webroot -w "$ACME_ROOT" -d "$ZED_HOST" \
        --email "$EMAIL" --agree-tos --no-eff-email --non-interactive \
        || die "Let's Encrypt could not verify $ZED_HOST. Check that port 80 is open to the internet (AWS security group) and DNS points here."
    ok 'certificate issued'
fi

step 'Switching the website to HTTPS'
write_nginx_site   # now the https template (certificate exists)
env_set "$ENV_FILE" APP_URL "https://$ZED_HOST"
env_set "$ENV_FILE" SESSION_SECURE_COOKIE true
artisan "$CURRENT" optimize
systemctl reload "php${PHP_VERSION}-fpm"
ok "APP_URL = https://$ZED_HOST, secure cookies on, http redirects to https"

step 'Testing automatic renewal (dry run, does not change the certificate)'
if certbot renew --dry-run --cert-name "$ZED_HOST" >/dev/null 2>&1; then ok 'renewal works'
else warn "renewal dry run failed: run 'sudo certbot renew --dry-run' to see why (usually port 80)."; fi

show_status
code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 60 --resolve "$ZED_HOST:443:127.0.0.1" "https://$ZED_HOST/" || true)
ok "https://$ZED_HOST/ -> HTTP $code"
echo
printf "\033[1;32mHTTPS is on:\033[0m https://%s/ (free Let's Encrypt certificate, renewed automatically)\n" "$ZED_HOST"
