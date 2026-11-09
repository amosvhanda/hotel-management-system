# Server deployment (Docker + Traefik)

This app deploys to the **same VPS as SyncCode**, behind the shared Traefik edge.

## Layout

| Path | Role |
|---|---|
| `/opt/edge` | Shared Traefik (already installed by SyncCode) |
| `/opt/apps/synccode` | SyncCode stack |
| `/opt/apps/hotel` | This hotel stack (own MySQL / PHP / nginx) |

Traffic: Internet → Traefik `:80`/`:443` → `hotel-nginx` (Host `hotel.synccode.co.zw`) → `hotel-app` PHP-FPM → `hotel-mysql`.

## Prerequisites

1. SyncCode Traefik edge is running (`docker network inspect edge` succeeds).
2. DNS **A record** `hotel.synccode.co.zw` → the VPS public IP.
3. GitHub repo secrets (Actions → Secrets):

| Secret | Value |
|---|---|
| `SSH_HOST` | VPS IP (same as SyncCode) |
| `SSH_USER` | deploy user (same as SyncCode) |
| `SSH_PRIVATE_KEY` | deploy key contents |
| `SSH_PATH` | `/opt/apps/hotel` |
| `DOMAIN` | `hotel.synccode.co.zw` |
| `SSH_PORT` | optional (default `22`) |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | optional (first install) |

Local reference file (never commit): copy `.env.deploy.example` → `.env.deploy`.

## First install

1. Push this branch (or merge) so deploy files are on `main`.
2. GitHub → **Actions** → **Deploy to server** → **Run workflow** with `first_install` = **true**.
3. That creates `/opt/apps/hotel/.env`, builds containers, migrates, and seeds once.
4. Edit server `.env` and set Paynow keys when ready:
   ```
   PAYNOW_INTEGRATION_ID=...
   PAYNOW_INTEGRATION_KEY=...
   ```
5. After DNS + HTTPS work, on the server set:
   ```
   APP_URL=https://hotel.synccode.co.zw
   SESSION_SECURE_COOKIE=true
   ```
   then `cd /opt/apps/hotel && bash deploy/release.sh` (or push another commit).

Staff login: `/admin/login` (seeded accounts — change passwords immediately).

## Later deploys

Every push to `main` runs tests, rsyncs code to `/opt/apps/hotel`, and runs `deploy/release.sh` (rebuild + migrate + optimize). Server `.env` and `storage/app` are never overwritten.

## Manual release

```bash
ssh your-user@YOUR_SERVER_IP
cd /opt/apps/hotel
bash deploy/release.sh
```
