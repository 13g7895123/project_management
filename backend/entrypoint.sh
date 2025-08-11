#!/bin/bash
set -e

echo "🚀 Starting Laravel application setup..."

# Create directories
mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache
mkdir -p /var/run/php
chown -R www-data:www-data storage bootstrap/cache /var/run/php
chmod -R 775 storage bootstrap/cache

# Wait for database with improved error handling
echo "⏳ Waiting for database connection..."
MAX_RETRIES=30
RETRY_COUNT=0

while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    if php artisan migrate:status > /dev/null 2>&1; then
        echo "✅ Database connection established"
        break
    else
        echo "Database not ready (attempt $((RETRY_COUNT + 1))/$MAX_RETRIES), waiting..."
        sleep 3
        RETRY_COUNT=$((RETRY_COUNT + 1))
    fi
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
    echo "❌ Database connection timeout after $MAX_RETRIES attempts"
    echo "Please check database configuration:"
    echo "  - DB_HOST: $DB_HOST"
    echo "  - DB_PORT: $DB_PORT"
    echo "  - DB_DATABASE: $DB_DATABASE"
    exit 1
fi

# Check if we should run migrations
echo "🔄 Checking migration status..."
if ! php artisan migrate:status; then
    echo "❌ Migration status check failed"
    exit 1
fi

echo "Running migrations..."
if ! php artisan migrate --force; then
    echo "❌ Migrations failed"
    exit 1
fi

echo "🌱 Running seeders..."
if ! php artisan db:seed --force; then
    echo "❌ Seeding failed"
    exit 1
fi

# Cache configuration
echo "🔧 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎉 Laravel setup completed successfully!"
echo "Starting services..."
exec "$@"