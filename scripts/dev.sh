#!/bin/bash

# Development environment startup script
echo "🚀 Starting Project Management System in Development Mode..."

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

# Start development environment
echo "🔧 Starting development services..."
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check service status
echo "📊 Service Status:"
docker-compose -f docker-compose.yml -f docker-compose.dev.yml ps

# Display URLs
echo ""
echo "🌐 Application URLs:"
echo "   Frontend (Development): http://localhost:${EXTERNAL_FRONTEND_PORT:-3000}"
echo "   Backend API: http://localhost:${EXTERNAL_BACKEND_PORT:-8000}/api"
echo "   phpMyAdmin: http://localhost:${EXTERNAL_PHPMYADMIN_PORT:-8080}"
echo ""
echo "🔍 To view logs:"
echo "   docker-compose -f docker-compose.yml -f docker-compose.dev.yml logs -f"
echo ""
echo "🛑 To stop:"
echo "   docker-compose -f docker-compose.yml -f docker-compose.dev.yml down"