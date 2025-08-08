#!/bin/bash

# Rollback script to restore original Docker configurations
echo "🔄 Rolling back to original Docker configurations..."

# Stop current containers if running
echo "🛑 Stopping current containers..."
docker-compose -f docker-compose.yml down 2>/dev/null || true
docker-compose -f docker-compose.yml -f docker-compose.dev.yml down 2>/dev/null || true
docker-compose -f docker-compose.yml -f docker-compose.prod.yml down 2>/dev/null || true

# Backup current integrated files
if [ -f docker-compose.yml ]; then
    echo "💾 Backing up integrated docker-compose.yml..."
    mv docker-compose.yml docker-compose.integrated.yml.backup
fi

if [ -f .env ]; then
    echo "💾 Backing up integrated .env..."
    mv .env .env.integrated.backup
fi

# Restore original backend configuration
if [ -f backend/docker-compose.yml.backup ]; then
    echo "🔧 Restoring original backend docker-compose.yml..."
    cp backend/docker-compose.yml.backup backend/docker-compose.yml
else
    echo "❌ Backend backup not found!"
fi

# Restore original frontend configuration
if [ -f frontend/docker-compose.yml.backup ]; then
    echo "🔧 Restoring original frontend docker-compose.yml..."
    cp frontend/docker-compose.yml.backup frontend/docker-compose.yml
else
    echo "❌ Frontend backup not found!"
fi

echo ""
echo "✅ Rollback completed!"
echo ""
echo "To start the original services:"
echo "   Backend:  cd backend && docker-compose up -d"
echo "   Frontend: cd frontend && docker-compose up -d"
echo ""
echo "To restore integrated setup:"
echo "   mv docker-compose.integrated.yml.backup docker-compose.yml"
echo "   mv .env.integrated.backup .env"