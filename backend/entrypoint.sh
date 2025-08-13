#!/bin/bash
set -e

echo "🚀 Starting Laravel application setup..."

# Create directories and ensure proper permissions
mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache
mkdir -p /var/run/php

# Initialize storage structure if using mounted volumes
if [ ! -d "storage/app" ]; then
    mkdir -p storage/app/public
fi
if [ ! -d "storage/framework" ]; then
    mkdir -p storage/framework/{cache,sessions,views,testing}
fi
if [ ! -d "storage/logs" ]; then
    mkdir -p storage/logs
fi

# Set proper permissions
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
if php artisan migrate:status > /dev/null 2>&1; then
    echo "✅ Migration status OK"
    
    echo "Running migrations..."
    if php artisan migrate --force; then
        echo "✅ Migrations completed"
    else
        echo "⚠️ Migrations failed, but continuing..."
    fi

    echo "🌱 Running seeders..."
    if php artisan db:seed --force; then
        echo "✅ Seeders completed"
    else
        echo "ℹ️ Seeding skipped (might already exist)"
    fi
else
    echo "⚠️ Database not ready for migrations, skipping..."
fi

# Cache configuration
echo "🔧 Optimizing application..."
php artisan config:cache || echo "⚠️ Config cache failed"
php artisan route:cache || echo "⚠️ Route cache failed" 
php artisan view:cache || echo "⚠️ View cache failed"

echo "🎉 Laravel setup completed successfully!"
echo "Starting services..."
exec "$@"