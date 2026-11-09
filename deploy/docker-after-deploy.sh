#!/usr/bin/env bash
# Run inside the app container after each release / compose up.
# Usage: docker compose exec -T app bash deploy/docker-after-deploy.sh
set -euo pipefail
cd /var/www/html

php artisan down || true

# Start clean: drop config/route/view/event caches and compiled views left by the
# previous release, so nothing old can be mixed with the new code.
php artisan optimize:clear || true
rm -f storage/framework/views/*.php bootstrap/cache/config.php bootstrap/cache/events.php || true
mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/app/public \
  bootstrap/cache

# PHP-FPM runs as www-data; keep the mounted storage volume writable for it.
# (docker compose exec runs this script as root and would otherwise leave
# sessions/logs/views owned by root → site-wide 500s.)
fix_storage() {
  if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache || true
    find storage -type d -exec chmod ug+rwx {} \; || true
    find storage -type f -exec chmod ug+rw {} \; || true
    chmod -R ug+rwx bootstrap/cache || true
  fi
}
fix_storage

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

fix_storage

php artisan up
# Tell the queue worker to pick up the new code after its current job.
php artisan queue:restart || true

# PHP-FPM caches compiled code and never re-checks files (opcache
# validate_timestamps=0). Reload it so it serves the freshly cached config/views,
# not versions it picked up before this script ran.
reload_fpm() {
  kill -USR2 1 2>/dev/null && sleep 2 || true
}
reload_fpm

smoke() {
  su -s /bin/sh www-data -c "php deploy/smoke.php"
}

echo "==> Smoke test (pages rendered as www-data, like the live site)"
if ! smoke; then
  echo "==> Smoke test failed — repairing caches and retrying once"
  php artisan optimize:clear || true
  rm -f storage/framework/views/*.php || true
  php artisan config:cache
  php artisan view:cache
  php artisan event:cache || true
  fix_storage
  reload_fpm
  if ! smoke; then
    echo "==> STILL FAILING. Last Laravel log lines:" >&2
    tail -n 60 storage/logs/laravel.log 2>/dev/null | cut -c1-400 >&2 || true
    exit 1
  fi
fi
echo "Docker release commands finished."
