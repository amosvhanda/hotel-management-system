#!/bin/sh
set -eu

mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/app/public \
  storage/logs \
  bootstrap/cache

# Host-mounted storage may arrive as root (or mixed); PHP-FPM runs as www-data
# and must be able to create upload folders (rooms, services, gallery, etc.).
if [ "$(id -u)" = "0" ]; then
  chown -R www-data:www-data storage bootstrap/cache || true
  find storage -type d -exec chmod ug+rwx {} \; || true
  find storage -type f -exec chmod ug+rw {} \; || true
  chmod -R ug+rwx bootstrap/cache || true
fi

# Symlink for local/dev; production nginx serves /storage via alias to this volume.
if [ ! -e public/storage ]; then
  php artisan storage:link >/dev/null 2>&1 || ln -sfn ../storage/app/public public/storage || true
fi

exec "$@"
