#!/usr/bin/env bash
# Read-only health check of the live server. Run from your Mac in the project folder:
#   bash deploy/diagnose.sh
# Writes the result to diagnose-output.txt (secrets in .env are hidden).
set -uo pipefail
cd "$(dirname "$0")/.."
set -a; . ./.env.deploy; set +a
KEY="${SSH_PRIVATE_KEY_FILE/#\~/$HOME}"
OUT=diagnose-output.txt
echo "Connecting to the server…"
ssh -i "$KEY" -p "${SSH_PORT:-22}" -o StrictHostKeyChecking=accept-new "${SSH_USER}@${SSH_HOST}" "cd '${SSH_PATH}' && bash -s" > "$OUT" 2>&1 <<'REMOTE'
echo "=== containers"; docker compose ps
echo; echo "=== laravel.log (last errors)"; docker compose exec -T app sh -c 'ls -la storage/logs; f=$(ls -t storage/logs/*.log 2>/dev/null | head -1); [ -n "$f" ] && grep -n "ERROR\|CRITICAL" "$f" | tail -n 5; echo ---; [ -n "$f" ] && tail -n 60 "$f" | cut -c1-400'
echo; echo "=== app container log"; docker compose logs --tail=40 app 2>&1 | cut -c1-300
echo; echo "=== nginx log"; docker compose logs --tail=15 nginx 2>&1 | cut -c1-300
echo; echo "=== file owners"; docker compose exec -T app sh -c 'id www-data; ls -ld storage storage/framework/* storage/logs bootstrap/cache public/upload; find storage bootstrap/cache -user root | head -n 15'
echo; echo "=== .env keys (values hidden)"; sed -E 's/=.+/=<set>/' .env | grep -vE '^\s*#|^\s*$' | tr '\n' ' '
echo; echo "=== disk"; df -h / | tail -1
REMOTE
echo "Done — saved to $OUT"
