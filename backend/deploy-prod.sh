#!/bin/bash

# Production deployment script for Laravel backend

set -e

echo "🚀 Starting production deployment..."

# Check if .env exists
if [ ! -f "src/.env" ]; then
    echo "❌ .env file not found. Copying from example..."
    cp src/.env.example src/.env
    echo "⚠️ Please edit src/.env with your production values"
fi

# Check if APP_KEY is set
if ! grep -q "APP_KEY=base64:" src/.env; then
    echo "🔑 Generating application key..."
    cd src
    php artisan key:generate
    cd ..
fi

# Pull latest changes (if in git directory)
if [ -d ".git" ]; then
    echo "📦 Pulling latest changes..."
    git pull
fi

# Build and start containers
echo "🐳 Building and starting containers..."
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml build
docker-compose -f docker-compose.prod.yml up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 30

# Run migrations
echo "🗄️ Running database migrations..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

# Optimize Laravel
echo "⚡ Optimizing Laravel..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# Set proper permissions
echo "🔐 Setting proper permissions..."
docker-compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data /var/www/html/storage
docker-compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "✅ Production deployment completed!"
echo "🌐 API should be available at: http://localhost:${APP_PORT:-9018}"

# Show container status
echo "📊 Container status:"
docker-compose -f docker-compose.prod.yml ps