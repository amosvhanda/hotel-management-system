#!/usr/bin/env bash
# First-time hotel app setup AFTER code is on the server.
# Joins the existing shared Traefik edge (/opt/edge from SyncCode), starts MySQL,
# wires Laravel, migrates, and seeds once.
#
# Does NOT install Docker or Traefik — those must already be running on this VPS.
set -euo pipefail
cd "$(dirname "$0")/.."

DOMAIN="${DOMAIN:-hotel.synccode.co.zw}"
SERVER_IP="${SERVER_IP:-}"
ADMIN_EMAIL="${ADMIN_EMAIL:-}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-}"
EDGE_PATH="${EDGE_PATH:-/opt/edge}"

if [ -z "$SERVER_IP" ]; then
  SERVER_IP="$(curl -4 -fsS --max-time 5 ifconfig.me 2>/dev/null || true)"
fi
if [ -z "$SERVER_IP" ]; then
  echo "Set SERVER_IP (your VPS public IP) so the app is reachable before DNS." >&2
  exit 1
fi

if [ ! -f docker-compose.yml ]; then
  echo "Run this from the app root (docker-compose.yml missing)." >&2
  exit 1
fi

if ! command -v docker >/dev/null 2>&1; then
  echo "Docker is not installed. Bootstrap the empty server from the SyncCode repo first." >&2
  exit 1
fi

if [ ! -f deploy/env.production.template ]; then
  echo "Missing deploy/env.production.template" >&2
  exit 1
fi

mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/app/public \
  storage/logs \
  bootstrap/cache

# Hex only — safe in shell, sed, MySQL, and .env (no quotes/slashes).
rand() {
  openssl rand -hex 24
}

env_get() {
  local key="$1"
  grep -E "^${key}=" .env | head -n1 | cut -d= -f2- || true
}

env_set() {
  local key="$1"
  local value="$2"
  if grep -q "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${value}|" .env
  else
    echo "${key}=${value}" >> .env
  fi
}

write_env_from_template() {
  local db_password="$1"
  local mysql_root_password="$2"
  local admin_email="$3"
  local admin_password="$4"

  sed \
    -e "s|__DOMAIN__|${DOMAIN}|g" \
    -e "s|__SERVER_IP__|${SERVER_IP}|g" \
    -e "s|__DB_PASSWORD__|${db_password}|g" \
    -e "s|__MYSQL_ROOT_PASSWORD__|${mysql_root_password}|g" \
    -e "s|__ADMIN_EMAIL__|${admin_email}|g" \
    -e "s|__ADMIN_PASSWORD__|${admin_password}|g" \
    deploy/env.production.template > .env
}

ensure_mysql_env_keys() {
  env_set DB_CONNECTION mysql
  env_set DB_HOST mysql
  env_set DB_PORT 3306
  env_set DB_DATABASE hotel
  env_set DB_USERNAME hotel
  env_set DOMAIN "${DOMAIN}"
  env_set SERVER_IP "${SERVER_IP}"
  env_set APP_URL "http://${SERVER_IP}"
  env_set SESSION_SECURE_COOKIE false
  if [ -z "$(env_get DB_PASSWORD)" ]; then
    env_set DB_PASSWORD "$(rand)"
  fi
  if [ -z "$(env_get MYSQL_ROOT_PASSWORD)" ]; then
    env_set MYSQL_ROOT_PASSWORD "$(rand)"
  fi
}

# .env is mounted read-only into containers — write APP_KEY on the host.
ensure_app_key() {
  if grep -q '^APP_KEY=base64:' .env; then
    return 0
  fi
  echo "==> Generating APP_KEY on host (container .env is read-only)"
  local key
  key="base64:$(openssl rand -base64 32 | tr -d '\n')"
  env_set APP_KEY "${key}"
}

ensure_edge() {
  echo "==> Ensuring shared Traefik edge is running (installed by SyncCode)"
  if [ ! -f "${EDGE_PATH}/docker-compose.yml" ]; then
    echo "Missing Traefik at ${EDGE_PATH}/docker-compose.yml" >&2
    echo "Bootstrap the empty server from the SyncCode repo first." >&2
    exit 1
  fi
  if [ ! -f "${EDGE_PATH}/.env" ]; then
    echo "ACME_EMAIL=admin@${DOMAIN}" > "${EDGE_PATH}/.env"
  fi
  (cd "$EDGE_PATH" && docker compose up -d)
  local i
  for i in $(seq 1 30); do
    if docker network inspect edge >/dev/null 2>&1; then
      echo "    Docker network 'edge' is ready."
      return 0
    fi
    sleep 1
  done
  echo "Docker network 'edge' missing after starting Traefik." >&2
  exit 1
}

wait_for_mysql() {
  echo "==> Waiting for MySQL"
  local i
  for i in $(seq 1 90); do
    if docker compose exec -T mysql \
      mysqladmin ping -h 127.0.0.1 -uroot -p"${MYSQL_ROOT_PASSWORD}" --silent >/dev/null 2>&1; then
      echo "    MySQL is up."
      return 0
    fi
    sleep 2
  done
  echo "MySQL did not become ready. Logs:" >&2
  docker compose logs --tail=80 mysql >&2 || true
  exit 1
}

wait_for_app() {
  echo "==> Waiting for app container"
  local i
  for i in $(seq 1 60); do
    if docker compose exec -T app php -r 'exit(0);' >/dev/null 2>&1; then
      echo "    App is up."
      return 0
    fi
    sleep 2
  done
  echo "App container did not become ready. Logs:" >&2
  docker compose logs --tail=80 app >&2 || true
  exit 1
}

verify_app_database() {
  echo "==> Verifying Laravel ↔ MySQL connection"
  # db:show needs doctrine/dbal (not installed). Probe via Laravel DB facade.
  docker compose exec -T app php -r '
  require "/var/www/html/vendor/autoload.php";
  $app = require_once "/var/www/html/bootstrap/app.php";
  $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
  Illuminate\Support\Facades\DB::connection()->getPdo();
  echo "Database connection OK\n";
  '
}

if [ ! -f .env ]; then
  if [ -z "$ADMIN_EMAIL" ]; then
    ADMIN_EMAIL="admin@${DOMAIN}"
  fi
  if [ -z "$ADMIN_PASSWORD" ]; then
    ADMIN_PASSWORD="$(rand)"
  fi
  write_env_from_template "$(rand)" "$(rand)" "$ADMIN_EMAIL" "$ADMIN_PASSWORD"
  echo "Created .env for https://${DOMAIN}"
  echo "  DB_HOST=mysql  DB_DATABASE=hotel  DB_USERNAME=hotel"
  echo "  ADMIN_EMAIL=${ADMIN_EMAIL}"
  echo "  ADMIN_PASSWORD=${ADMIN_PASSWORD}"
  echo "  (Seeded logins are also in DatabaseSeeder — change passwords after first login.)"
  echo "  (Set PAYNOW_INTEGRATION_ID / PAYNOW_INTEGRATION_KEY in .env when ready.)"
else
  echo ".env already exists — ensuring MySQL + DOMAIN keys."
  ensure_mysql_env_keys
fi

ensure_app_key

DB_PASSWORD="$(env_get DB_PASSWORD)"
MYSQL_ROOT_PASSWORD="$(env_get MYSQL_ROOT_PASSWORD)"
DB_DATABASE="$(env_get DB_DATABASE)"
DB_USERNAME="$(env_get DB_USERNAME)"

if [ -z "${DB_PASSWORD}" ] || [ -z "${MYSQL_ROOT_PASSWORD}" ]; then
  echo "DB_PASSWORD and MYSQL_ROOT_PASSWORD must be set in .env" >&2
  exit 1
fi

# Export for compose variable interpolation (${DB_PASSWORD:?...}).
export DB_PASSWORD MYSQL_ROOT_PASSWORD
export DB_DATABASE="${DB_DATABASE:-hotel}"
export DB_USERNAME="${DB_USERNAME:-hotel}"
export DOMAIN
export SERVER_IP

ensure_edge

echo "==> Validating compose file"
docker compose config >/dev/null

echo "==> Building and starting this app (same container names; remove orphans)"
docker compose up -d --build --remove-orphans

wait_for_mysql
wait_for_app

echo "==> Ensuring database and user exist"
docker compose exec -T mysql \
  mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e "
    CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
    ALTER USER '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
    GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'%';
    FLUSH PRIVILEGES;
  "

verify_app_database

echo "==> Migrate + optimize"
docker compose exec -T app bash deploy/docker-after-deploy.sh

if [ ! -f storage/app/.initial_seed_done ]; then
  echo "==> Seeding (first time only)"
  docker compose exec -T app php artisan db:seed --force
  touch storage/app/.initial_seed_done
else
  echo "==> Initial seed already done — skipping db:seed"
fi

echo "==> Final checks"
docker compose exec -T app php artisan migrate:status
docker compose ps
docker ps --filter name=edge-traefik --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}'

echo "==> Prune dangling images"
docker image prune -f >/dev/null || true

echo
echo "Database installed and connected."
echo "  By IP:   http://${SERVER_IP}/"
echo "  Admin:   http://${SERVER_IP}/admin/login"
echo "  Domain:  https://${DOMAIN} (after DNS A record points here)"
echo "  After HTTPS works: set APP_URL=https://${DOMAIN} and SESSION_SECURE_COOKIE=true in .env"
