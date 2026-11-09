#!/usr/bin/env bash
# Run inside the app container after each release / compose up.
# Usage: docker compose exec -T app bash deploy/docker-after-deploy.sh
set -euo pipefail
cd /var/www/html

php artisan down || true
mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/app/public \
  bootstrap/cache

echo "Checking database connection..."
php artisan db:show

php artisan migrate --force
php artisan storage:link || true

# Do not run route:cache — routes/api.php uses a closure which cannot be cached.
php artisan config:cache
php artisan view:cache
php artisan event:cache || true

php artisan up
echo "Docker release commands finished."
