#!/bin/bash

echo "🚀 Starting Laravel application setup..."

# Create directories and ensure proper permissions
mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache
mkdir -p /var/run/php

# Set proper permissions
chown -R www-data:www-data storage bootstrap/cache /var/run/php || true
chmod -R 775 storage bootstrap/cache || true

# Try database operations but don't fail if they don't work
echo "🔄 Attempting database operations..."
if php artisan migrate:status > /dev/null 2>&1; then
    echo "✅ Database accessible, running migrations..."
    php artisan migrate --force || echo "⚠️ Migration failed, continuing..."
    php artisan db:seed --force || echo "ℹ️ Seeding skipped, continuing..."
else
    echo "⚠️ Database not accessible, skipping database operations..."
fi

# Cache configuration - don't fail if these don't work
echo "🔧 Attempting to optimize application..."
php artisan config:cache || echo "ℹ️ Config cache skipped"
php artisan route:cache || echo "ℹ️ Route cache skipped"
php artisan view:cache || echo "ℹ️ View cache skipped"

echo "🎉 Laravel setup completed successfully!"
echo "Starting services..."
exec "$@"