#!/bin/bash
set -e

echo "Starting Activity Hub..."
echo "DB_HOST: $DB_HOST"
echo "DB_DATABASE: $DB_DATABASE"

# Generate .env from Render environment variables at runtime
cat > /var/www/.env << EOF
APP_NAME=${APP_NAME:-Laravel}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-forge}
DB_USERNAME=${DB_USERNAME:-forge}
DB_PASSWORD=${DB_PASSWORD:-}

SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=${CACHE_STORE:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
LOG_CHANNEL=${LOG_CHANNEL:-stack}
FILESYSTEM_DISK=local
BROADCAST_CONNECTION=log

MAIL_MAILER=log
EOF

echo ".env file generated"
echo "Running migrations..."
php artisan migrate --force || echo "Migration warning (may be expected)"
echo "Starting server on 0.0.0.0:10000..."
exec php artisan serve --host=0.0.0.0 --port=10000
