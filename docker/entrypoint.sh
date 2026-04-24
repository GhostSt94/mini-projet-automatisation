#!/usr/bin/env bash
set -e

cd /var/www/html

if [ ! -f .env ]; then
    echo "[entrypoint] No .env found, copying from .env.example..."
    cp .env.example .env
fi

echo "[entrypoint] Waiting for MySQL at ${DB_HOST:-db}:${DB_PORT:-3306}..."
until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
"; do
    echo "[entrypoint] MySQL not ready, retrying in 2s..."
    sleep 2
done
echo "[entrypoint] MySQL is up."

if [ -z "${APP_KEY}" ] || ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "[entrypoint] Generating APP_KEY..."
    php artisan key:generate --force
fi

echo "[entrypoint] Running migrations and seeders..."
php artisan migrate --force --seed

echo "[entrypoint] Caching config and routes..."
php artisan config:cache
php artisan route:cache

echo "[entrypoint] Starting: $*"
exec "$@"
