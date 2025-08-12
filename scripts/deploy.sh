#!/bin/bash

# Deployment script for project management system
# Usage: ./scripts/deploy.sh [--no-build] [--no-cache]

set -e  # Exit on any error

# Parse arguments
BUILD_FLAG="--build"
CACHE_OPERATIONS=true

while [[ $# -gt 0 ]]; do
    case $1 in
        --no-build)
            BUILD_FLAG=""
            shift
            ;;
        --no-cache)
            CACHE_OPERATIONS=false
            shift
            ;;
        *)
            echo "Unknown argument: $1"
            echo "Usage: $0 [--no-build] [--no-cache]"
            exit 1
            ;;
    esac
done

echo "🚀 Starting deployment process..."
echo "================================"

# Check if docker-compose exists
if ! command -v docker-compose &> /dev/null; then
    if ! command -v docker &> /dev/null || ! docker compose version &> /dev/null; then
        echo "❌ Neither docker-compose nor 'docker compose' is available"
        exit 1
    fi
    DOCKER_COMPOSE="docker compose"
else
    DOCKER_COMPOSE="docker-compose"
fi

echo "Using: $DOCKER_COMPOSE"

# Pull latest changes if in git repo
if [ -d ".git" ]; then
    echo "📥 Pulling latest changes from git..."
    git pull
fi

echo "🐳 Building and starting containers..."
if [ -n "$BUILD_FLAG" ]; then
    $DOCKER_COMPOSE up $BUILD_FLAG -d
else
    $DOCKER_COMPOSE up -d
fi

# Wait for containers to be ready
echo "⏳ Waiting for containers to start..."
sleep 15

# Check if backend container is running
if ! $DOCKER_COMPOSE ps backend | grep -q "running\|Up"; then
    echo "❌ Backend container is not running"
    $DOCKER_COMPOSE logs backend
    exit 1
fi

echo "🗄️ Setting up database..."
# Run database migrations and seeders
if $DOCKER_COMPOSE exec -T backend php artisan migrate:status &>/dev/null; then
    echo "Running migrations..."
    $DOCKER_COMPOSE exec -T backend php artisan migrate --force
    
    echo "Running seeders..."
    $DOCKER_COMPOSE exec -T backend php artisan db:seed --force || echo "ℹ️ Seeding skipped (data might already exist)"
else
    echo "❌ Cannot connect to database or backend is not ready"
    echo "Backend logs:"
    $DOCKER_COMPOSE logs backend | tail -20
fi

# Cache operations
if [ "$CACHE_OPERATIONS" = true ]; then
    echo "⚡ Optimizing application..."
    $DOCKER_COMPOSE exec -T backend php artisan config:cache || echo "⚠️ Config cache failed"
    $DOCKER_COMPOSE exec -T backend php artisan route:cache || echo "⚠️ Route cache failed"  
    $DOCKER_COMPOSE exec -T backend php artisan view:cache || echo "⚠️ View cache failed"
fi

# Verify deployment
echo "🔍 Verifying deployment..."
echo "Container status:"
$DOCKER_COMPOSE ps

echo "🌐 Testing API endpoints..."
if command -v curl &> /dev/null; then
    # Test if running locally
    if curl -s http://localhost:3000 &>/dev/null; then
        echo "✅ Frontend is accessible at http://localhost:3000"
    fi
    
    if curl -s http://localhost:8000/api/test &>/dev/null; then
        echo "✅ Backend API is accessible at http://localhost:8000"
    fi
else
    echo "ℹ️ curl not available, skipping API tests"
fi

echo ""
echo "✅ Deployment completed successfully!"
echo "================================"
echo "Frontend: http://localhost:3000"
echo "Backend API: http://localhost:8000/api"
echo "Database: localhost:3306"
echo ""
echo "Admin credentials:"
echo "Email: admin@example.com"
echo "Password: password"