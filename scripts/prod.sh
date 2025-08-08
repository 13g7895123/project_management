#!/bin/bash

# Production environment deployment script
echo "🚀 Deploying Project Management System in Production Mode..."

# Load environment variables
if [ -f .env ]; then
    echo "📋 Loading environment variables from .env"
    export $(grep -v '^#' .env | xargs)
fi

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Build and start production environment
echo "🏗️  Building production services..."
docker-compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache

echo "🔧 Starting production services..."
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 15

# Run database migrations
echo "🗃️  Running database migrations..."
docker-compose -f docker-compose.yml -f docker-compose.prod.yml exec backend php artisan migrate --force

# Check service status
echo "📊 Service Status:"
docker-compose -f docker-compose.yml -f docker-compose.prod.yml ps

# Display URLs
echo ""
echo "🌐 Application URLs:"
echo "   Frontend: http://localhost:${EXTERNAL_FRONTEND_PORT:-3000}"
echo "   Backend API: http://localhost:${EXTERNAL_BACKEND_PORT:-8000}/api"
echo ""
echo "🔍 To view logs:"
echo "   docker-compose -f docker-compose.yml -f docker-compose.prod.yml logs -f"
echo ""
echo "🛑 To stop:"
echo "   docker-compose -f docker-compose.yml -f docker-compose.prod.yml down"