#!/bin/bash

echo "🚀 Starting Laravel application setup..."

# Create all necessary directories
mkdir -p storage/logs 
mkdir -p storage/framework/{cache,sessions,views,testing}
mkdir -p storage/app/public
mkdir -p bootstrap/cache
mkdir -p /var/run/php
mkdir -p /var/lib/php/sessions
mkdir -p /var/lib/php/wsdlcache

# Set proper ownership and permissions for Laravel
chown -R www-data:www-data storage bootstrap/cache /var/run/php /var/lib/php
chmod -R 775 storage bootstrap/cache
chmod 755 /var/lib/php/sessions /var/lib/php/wsdlcache

# Ensure log directory is writable
touch storage/logs/laravel.log || true
chown www-data:www-data storage/logs/laravel.log || true
chmod 664 storage/logs/laravel.log || true

echo "✅ Permissions set for www-data user"

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