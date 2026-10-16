#!/usr/bin/env bash
# STEP 3 - on the Ubuntu server, once. Creates the zedcms database and its own
# MySQL user, imports the data and saves the connection settings in
# /var/www/zedcms/shared/.env for the website.
#
#   sudo bash init-database.sh                         # database/zedcms.sql from the package
#   sudo bash init-database.sh --dump /tmp/prod.sql    # a production export (.sql or .sql.gz)
# Options:
#   --skip-upgrade   the export already has upgrade_mysql8.sql / upgrade_2026_10_security.sql applied
#   --replace        drop and import an existing zedcms database again (asks to confirm)
# Usually run for you by Publish-ZedRelease.ps1 -Setup from the Windows PC.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/zed-common.sh"
require_root
PACKAGE="$(dirname "$SCRIPT_DIR")"

DUMP="$PACKAGE/database/zedcms.sql"; SKIP_UPGRADE=0; REPLACE=0
DB_NAME="${DB_NAME:-zedcms}"; DB_USER="${DB_USER:-zed}"
while [[ $# -gt 0 ]]; do
    case $1 in
        --dump) DUMP=$2; shift 2 ;;
        --skip-upgrade) SKIP_UPGRADE=1; shift ;;
        --replace) REPLACE=1; shift ;;
        *) die "Unknown option $1" ;;
    esac
done
[[ -f $DUMP ]] || die "Database dump not found: $DUMP"
start_log database

# MySQL administrator: Ubuntu's root account signs in through sudo (no password);
# otherwise ask for the root password.
MYSQL=(mysql --protocol=socket -uroot)
if ! "${MYSQL[@]}" -e 'SELECT 1' >/dev/null 2>&1; then
    read -r -s -p 'MySQL root password: ' rootpw; echo
    OPTS=$(mktemp); chmod 600 "$OPTS"
    printf '[client]\nuser=root\npassword="%s"\n' "${rootpw//\"/\\\"}" > "$OPTS"
    trap 'rm -f "$OPTS"' EXIT
    MYSQL=(mysql --defaults-extra-file="$OPTS")
    "${MYSQL[@]}" -e 'SELECT 1' >/dev/null || die 'Cannot sign in to MySQL as root.'
fi
sql() { "${MYSQL[@]}" --batch --skip-column-names -e "$1"; }
import() {
    echo "    importing $(basename "$1")"
    if [[ $1 == *.gz ]]; then zcat "$1" | "${MYSQL[@]}" --default-character-set=utf8mb4 "$DB_NAME"
    else "${MYSQL[@]}" --default-character-set=utf8mb4 "$DB_NAME" < "$1"; fi
}

step 'MySQL'
ok "$(sql 'SELECT VERSION()')"

step "Database $DB_NAME"
do_import=1
if [[ -n $(sql "SHOW DATABASES LIKE '$DB_NAME'") ]]; then
    if (( REPLACE )); then
        read -r -p "Type DELETE to drop the existing database $DB_NAME and import it again: " answer
        [[ $answer == DELETE ]] || die 'Stopped; nothing was changed.'
        sql "DROP DATABASE \`$DB_NAME\`"
    else
        ok 'already exists; keeping its data (use --replace to import again)'
        do_import=0
    fi
fi
if (( do_import )); then
    sql "CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    import "$DUMP"
    if (( ! SKIP_UPGRADE )); then
        import "$PACKAGE/database/upgrade_mysql8.sql"
        import "$PACKAGE/database/upgrade_2026_10_security.sql"
    fi
    ok "imported ($(sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME'") tables)"
fi

step "Database user $DB_USER (used by the website)"
init_shared_env "$PACKAGE/app/.env.example"
# keep the password the website already uses (running this again must not break the site)
password=$(env_get "$ENV_FILE" DB_PASSWORD)
[[ -n $password && $(env_get "$ENV_FILE" DB_USERNAME) == "$DB_USER" ]] || password=$(random_secret 32)
for host in localhost 127.0.0.1; do
    sql "CREATE USER IF NOT EXISTS '$DB_USER'@'$host' IDENTIFIED BY '$password'"
    sql "ALTER USER '$DB_USER'@'$host' IDENTIFIED BY '$password'"
    sql "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES, LOCK TABLES ON \`$DB_NAME\`.* TO '$DB_USER'@'$host'"
done
sql 'FLUSH PRIVILEGES'
ok "user $DB_USER can use $DB_NAME only"

step 'Saving the connection settings for the website'
env_set "$ENV_FILE" DB_CONNECTION mysql
env_set "$ENV_FILE" DB_HOST 127.0.0.1
env_set "$ENV_FILE" DB_PORT 3306
env_set "$ENV_FILE" DB_DATABASE "$DB_NAME"
env_set "$ENV_FILE" DB_USERNAME "$DB_USER"
env_set "$ENV_FILE" DB_PASSWORD "$password"
ok "written to $ENV_FILE"

echo
printf '\033[1;32mDatabase ready.\033[0m Next: deploy.sh\n'
