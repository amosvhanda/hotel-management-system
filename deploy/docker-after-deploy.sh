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
# db:show needs doctrine/dbal (not installed). Probe via Laravel DB facade.
php -r '
require "/var/www/html/vendor/autoload.php";
$app = require_once "/var/www/html/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Illuminate\Support\Facades\DB::connection()->getPdo();
echo "Database connection OK\n";
'

php artisan migrate --force

# Logins on our domain: moves admin@gmail.com / user@gmail.com to
# ADMIN_EMAIL (with ADMIN_PASSWORD) and guest@<DOMAIN>. Safe every deploy.
php artisan db:seed --class=UsersTableSeeder --force || echo "WARNING: could not update starter logins" >&2

# One time only: Mopane Heights branding, rooms, photos and page text.
# The marker lives in storage/ (kept between deploys), so later edits made in
# Admin are never overwritten. Delete storage/app/.demo_content_done to re-run.
if [ ! -f storage/app/.demo_content_done ]; then
  echo "Loading Mopane Heights content (first time only)..."
  if php artisan db:seed --class=ZimbabweDemoSeeder --force; then
    touch storage/app/.demo_content_done
  else
    echo "WARNING: demo content failed to load — site still goes live." >&2
  fi
fi
php artisan storage:link || true

# Do not run route:cache — routes/api.php uses a closure which cannot be cached.
php artisan config:cache
php artisan view:cache
php artisan event:cache || true

php artisan up
echo "Docker release commands finished."
