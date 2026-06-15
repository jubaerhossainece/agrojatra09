#!/bin/sh
set -e

# ── Wait for host MySQL to accept connections ──────────────────────────────
echo "[entrypoint] Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
until nc -z "${DB_HOST}" "${DB_PORT}" 2>/dev/null; do
  sleep 2
done
echo "[entrypoint] MySQL is up."

# ── Write runtime env values into .env so artisan commands pick them up ───
# (docker-compose environment vars are already in the shell; php artisan reads
# the .env file, so we sync the critical DB keys into it)
php artisan config:clear

# ── Run migrations (idempotent — safe to repeat on every restart) ─────────
php artisan migrate --force

# ── Cache config/routes/views for production performance ──────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link 2>/dev/null || true

echo "[entrypoint] Bootstrap complete. Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
