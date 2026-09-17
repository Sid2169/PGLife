#!/usr/bin/env bash
#
# Release-based deployment for a VPS (Apache or nginx + PHP-FPM + MySQL).
#
# Usage (from a checkout of this repo):
#   DB_PASS='strong-password' sudo -E ./deploy/deploy-vps.sh
#
# What it does:
#   1. Copies the app into /var/www/pglife/releases/<timestamp>
#   2. Atomically flips the /var/www/pglife/current symlink
#   3. Imports pglife.sql the first time (empty database only)
#   4. Reloads the web server
#
# Roll back:
#   ln -sfn /var/www/pglife/releases/<previous> /var/www/pglife/current
#   systemctl reload apache2   # or nginx
#
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/pglife}"
RELEASES="$APP_DIR/releases"
STAMP="$(date +%Y%m%d-%H%M%S)"
TARGET="$RELEASES/$STAMP"
SRC="$(cd "$(dirname "$0")/.." && pwd)"
WEB_SERVICE="${WEB_SERVICE:-apache2}"
WEB_USER="${WEB_USER:-www-data}"

DB_NAME="${DB_NAME:-pglife}"
DB_USER="${DB_USER:-pglife}"
DB_PASS="${DB_PASS:-}"

command -v rsync >/dev/null 2>&1 || { echo "rsync is required"; exit 1; }

echo "==> Creating release $TARGET"
mkdir -p "$TARGET"
rsync -a --delete \
    --exclude '.git' \
    --exclude 'solutions' \
    --exclude '.env' \
    --exclude 'deploy' \
    --exclude '*.md' \
    "$SRC"/ "$TARGET"/

ln -sfn "$TARGET" "$APP_DIR/current"
chown -R "$WEB_USER":"$WEB_USER" "$APP_DIR"

if command -v mysql >/dev/null 2>&1 && [ -n "$DB_PASS" ]; then
    TABLES="$(mysql -N -B -u "$DB_USER" -p"$DB_PASS" -e "SHOW TABLES IN \`$DB_NAME\`;" 2>/dev/null | wc -l || true)"
    if [ "${TABLES:-0}" -eq 0 ]; then
        echo "==> Importing pglife.sql into $DB_NAME (first deploy)"
        mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$TARGET/pglife.sql"
    else
        echo "==> Database $DB_NAME already has tables; skipping import"
    fi
fi

echo "==> Reloading $WEB_SERVICE"
systemctl reload "$WEB_SERVICE"

echo "==> Deployed release $STAMP"
echo "    Previous releases are in $RELEASES"
