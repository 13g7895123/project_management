#!/bin/bash

# API Debugging Script for VPS Deployment
# Run this script to diagnose API 404 issues

echo "🔍 API Debugging Script"
echo "======================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print status
print_status() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓${NC} $2"
    else
        echo -e "${RED}✗${NC} $2"
    fi
}

# Function to print info
print_info() {
    echo -e "${YELLOW}ℹ${NC} $1"
}

echo ""
print_info "1. Checking Service Status..."

# Check if we're in the right directory
if [ ! -d "backend" ] || [ ! -d "frontend" ]; then
    echo -e "${RED}Error: Please run this script from the project root directory${NC}"
    exit 1
fi

# Check backend services
print_info "Backend Services:"
cd backend
docker compose ps
backend_running=$?

if [ $backend_running -eq 0 ]; then
    print_status 0 "Backend Docker services are running"
else
    print_status 1 "Backend Docker services are not running properly"
    echo "Try running: cd backend && docker compose up -d"
fi

# Check frontend services  
print_info "Frontend Services:"
cd ../frontend
if [ -f "docker-compose.yml" ]; then
    docker compose ps
    frontend_running=$?
    print_status $frontend_running "Frontend Docker services status checked"
else
    echo "No frontend docker-compose.yml found - frontend might be running differently"
fi

echo ""
print_info "2. Testing Backend API Directly..."

# Test backend API endpoints
backend_host="localhost"
backend_port="9018"

# Check if backend is accessible
print_info "Testing backend connectivity..."
backend_response=$(curl -s -w "%{http_code}" -o /tmp/api_test.json "http://$backend_host:$backend_port/api/clients" 2>/dev/null)

if [ "$backend_response" = "200" ]; then
    print_status 0 "Backend API is accessible at http://$backend_host:$backend_port"
    echo "Sample response:"
    head -c 200 /tmp/api_test.json | jq . 2>/dev/null || head -c 200 /tmp/api_test.json
    echo "..."
else
    print_status 1 "Backend API is NOT accessible (HTTP $backend_response)"
    echo "Full response:"
    cat /tmp/api_test.json 2>/dev/null || echo "No response received"
fi

echo ""
print_info "3. Testing Specific Endpoints..."

endpoints=(
    "GET /api/clients"
    "GET /api/projects"
    "POST /api/projects"
)

for endpoint in "${endpoints[@]}"; do
    method=$(echo $endpoint | cut -d' ' -f1)
    path=$(echo $endpoint | cut -d' ' -f2)
    
    echo -n "Testing $endpoint: "
    
    if [ "$method" = "GET" ]; then
        response_code=$(curl -s -w "%{http_code}" -o /dev/null "http://$backend_host:$backend_port$path")
    elif [ "$method" = "POST" ]; then
        response_code=$(curl -s -w "%{http_code}" -o /dev/null -X POST \
            -H "Content-Type: application/json" \
            -d '{"name":"Test","client_id":1,"category":"website","amount":10000,"contact_date":"2025-08-07","status":"contacted"}' \
            "http://$backend_host:$backend_port$path")
    fi
    
    if [ "$response_code" = "200" ] || [ "$response_code" = "201" ]; then
        echo -e "${GREEN}$response_code OK${NC}"
    else
        echo -e "${RED}$response_code ERROR${NC}"
    fi
done

echo ""
print_info "4. Checking Frontend API Configuration..."

# Check if frontend is trying to access the right URLs
if [ -f "../frontend/src/composables/useApi.js" ]; then
    echo "Frontend API configuration:"
    grep -n "baseURL\|BACKEND\|9018" ../frontend/src/composables/useApi.js || echo "No specific backend URL found"
else
    echo "Frontend useApi.js not found"
fi

# Check environment files
if [ -f "../frontend/.env" ]; then
    echo "Frontend environment variables:"
    grep -v "^#" ../frontend/.env | grep -E "BACKEND|API" || echo "No backend-related environment variables"
else
    echo "No frontend .env file found"
fi

echo ""
print_info "5. Network Connectivity Test..."

# Test different possible URLs that frontend might use
possible_urls=(
    "http://localhost:9018/api/clients"
    "http://127.0.0.1:9018/api/clients"  
    "http://$(hostname):9018/api/clients"
    "http://$(hostname -I | cut -d' ' -f1):9018/api/clients"
)

echo "Testing possible backend URLs that frontend might use:"
for url in "${possible_urls[@]}"; do
    echo -n "  $url: "
    response=$(curl -s -w "%{http_code}" -o /dev/null --connect-timeout 5 "$url" 2>/dev/null)
    if [ "$response" = "200" ]; then
        echo -e "${GREEN}$response OK${NC}"
    else
        echo -e "${RED}$response FAIL${NC}"
    fi
done

echo ""
print_info "6. Port and Process Check..."

# Check what's running on port 9018
echo "Processes on port 9018:"
netstat -tlnp 2>/dev/null | grep :9018 || ss -tlnp 2>/dev/null | grep :9018 || echo "No processes found on port 9018"

# Check what's running on port 3000 (frontend)
echo "Processes on port 3000:"
netstat -tlnp 2>/dev/null | grep :3000 || ss -tlnp 2>/dev/null | grep :3000 || echo "No processes found on port 3000"

echo ""
print_info "7. Docker Logs Check..."

echo "Recent backend logs:"
cd ../backend
docker compose logs --tail=10 app 2>/dev/null || echo "Could not fetch backend logs"

echo ""
echo "Recent frontend logs (if using Docker):"
cd ../frontend
if [ -f "docker-compose.yml" ]; then
    docker compose logs --tail=10 2>/dev/null || echo "Could not fetch frontend logs"
fi

echo ""
print_info "8. Recommended Actions..."

echo ""
echo "🔧 If APIs are returning 404:"
echo "   1. Ensure backend is running: cd backend && docker compose up -d"
echo "   2. Check backend logs: docker compose logs app"
echo "   3. Verify Laravel routes: docker exec backend-app-1 php artisan route:list"
echo "   4. Test API directly: curl http://localhost:9018/api/clients"
echo ""
echo "🔧 If frontend can't connect to backend:"
echo "   1. Update frontend .env with correct backend URL"
echo "   2. Check CORS configuration in backend/src/config/cors.php"
echo "   3. Ensure firewall allows port 9018"
echo "   4. Check if frontend and backend are on same network"
echo ""
echo "🔧 For production deployment:"
echo "   1. Update frontend .env.production with your VPS IP"
echo "   2. Restart services after configuration changes"
echo "   3. Test with: curl http://YOUR-VPS-IP:9018/api/clients"

# Cleanup
rm -f /tmp/api_test.json

echo ""
print_info "Debugging complete!"
echo ""