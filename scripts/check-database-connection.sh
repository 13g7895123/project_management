#!/bin/bash

echo "🔍 Database Connection Troubleshooting Script"
echo "=============================================="

# Check if API is responding
echo "1. Testing basic API endpoint..."
curl -s https://project.mercylife.cc/api/test | jq '.' 2>/dev/null || echo "❌ Basic API test failed"

# Check database health endpoint
echo -e "\n2. Testing database health endpoint..."
curl -s https://project.mercylife.cc/api/health | jq '.' 2>/dev/null || echo "❌ Health endpoint failed"

# Test database-dependent login
echo -e "\n3. Testing login endpoint (will show specific database error)..."
curl -X POST https://project.mercylife.cc/api/auth/login \
    -H "Content-Type: application/json" \
    -d '{"login":"admin@example.com","password":"password"}' \
    -s | head -5

echo -e "\n\n💡 Troubleshooting Steps:"
echo "========================="
echo "1. Check if database container is running:"
echo "   docker ps | grep mysql"
echo ""
echo "2. Check database logs:"
echo "   docker logs <mysql-container-name>"
echo ""
echo "3. Test database connection from backend container:"
echo "   docker exec <backend-container> php artisan migrate:status"
echo ""
echo "4. Try to setup database manually:"
echo "   docker exec <backend-container> php artisan migrate --force"
echo "   docker exec <backend-container> php artisan db:seed --force"
echo ""
echo "5. Check backend environment variables:"
echo "   docker exec <backend-container> env | grep DB_"
echo ""
echo "Expected admin credentials: admin@example.com / password"