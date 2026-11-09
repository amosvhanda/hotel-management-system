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

echo "==> Removing unused dangling images (keeps running images)"
docker image prune -f

echo "==> Disk after prune"
df -h / | tail -1
docker system df

echo "==> Current containers for this project"
docker compose ps
echo "Release done — no extra stacks left behind."
