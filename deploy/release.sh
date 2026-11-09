#!/usr/bin/env bash
# Rebuild/restart THIS app only — same container names, no duplicates.
# Removes orphan containers and unused dangling images so disk does not fill.
#
# Usage (from app root on the server):
#   bash deploy/release.sh
set -euo pipefail
cd "$(dirname "$0")/.."

if [ ! -f docker-compose.yml ]; then
  echo "Run from the app root (docker-compose.yml missing)." >&2
  exit 1
fi

# --- Always deploy with HTTPS -------------------------------------------------
# .env is mounted read-only into containers, so fix it on the host BEFORE
# containers start and config:cache runs.
env_get() { grep -E "^$1=" .env | head -n1 | cut -d= -f2- || true; }
env_set() {
  if grep -q "^$1=" .env; then sed -i "s|^$1=.*|$1=$2|" .env; else echo "$1=$2" >> .env; fi
}
DOMAIN="${DOMAIN:-$(env_get DOMAIN)}"
DOMAIN="${DOMAIN:-hotel.synccode.co.zw}"
echo "==> Enforcing HTTPS settings for https://${DOMAIN}"
env_set DOMAIN "${DOMAIN}"
env_set APP_URL "https://${DOMAIN}"
env_set ASSET_URL "https://${DOMAIN}"
env_set SESSION_SECURE_COOKIE true
# Background sending (queue container) + hotel time zone.
env_set QUEUE_CONNECTION database
grep -q '^APP_TIMEZONE=' .env || env_set APP_TIMEZONE Africa/Harare

# Paynow / EcoCash keys from GitHub Actions secrets (only when provided).
for key in PAYNOW_INTEGRATION_ID PAYNOW_INTEGRATION_KEY PAYNOW_AUTH_EMAIL; do
  val="${!key:-}"
  if [ -n "$val" ]; then
    env_set "$key" "$val"
    echo "    ${key} set from deploy secrets"
  fi
done

echo "==> Updating stack in place (same names: mysql/app/nginx/scheduler)"
# Rebuild changed images; recreate only containers whose config/image changed.
# Do not force-recreate healthy unchanged services.
docker compose up -d --build --remove-orphans

echo "==> Waiting for app"
for i in $(seq 1 60); do
  if docker compose exec -T app php -r 'exit(0);' >/dev/null 2>&1; then
    break
  fi
  sleep 2
done

echo "==> Migrate + optimize"
docker compose exec -T app bash deploy/docker-after-deploy.sh

# Host-side belt-and-suspenders: keep mounted ./storage owned by www-data (uid 33).
if [ "$(id -u)" = "0" ] && [ -d storage ]; then
  chown -R 33:33 storage || true
fi

echo "==> Checking the queue worker is running"
docker compose ps queue --status running --quiet | grep -q . || { echo "Queue worker is not running:" >&2; docker compose logs --tail=40 queue >&2; exit 1; }

echo "==> Removing unused dangling images (keeps running images)"
docker image prune -f

echo "==> Disk after prune"
df -h / | tail -1
docker system df

echo "==> Current containers for this project"
docker compose ps
echo "Release done — no extra stacks left behind."
