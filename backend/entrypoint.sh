#!/bin/bash
set -e

echo "Starting Laravel application setup..."

# Create directories
mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache
mkdir -p /var/run/php
chown -R www-data:www-data storage bootstrap/cache /var/run/php
chmod -R 775 storage bootstrap/cache

# Wait for database
echo "Waiting for database..."
until php artisan migrate:status > /dev/null 2>&1; do
    echo "Database not ready, waiting..."
    sleep 3
done

echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force

echo "Starting services..."
exec "$@"