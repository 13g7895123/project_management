#!/bin/bash

# VPS Deployment Testing Script
# Run this script on your VPS to verify deployment

echo "🚀 Starting VPS Deployment Testing..."
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
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
print_info "1. Checking Git Status..."
cd /home/jarvis/project/project_management
git status --porcelain
git log --oneline -3

echo ""
print_info "2. Checking Docker Services..."

# Check backend services
echo "Backend services:"
cd backend
docker compose ps
backend_status=$?
print_status $backend_status "Backend containers running"

# Check frontend services
echo "Frontend services:"
cd ../frontend
docker compose ps
frontend_status=$?
print_status $frontend_status "Frontend containers running"

echo ""
print_info "3. Testing Backend API Endpoints..."

# Test clients API
echo "Testing /api/clients..."
clients_response=$(curl -s -w "%{http_code}" -o /tmp/clients_test.json "http://localhost:9018/api/clients")
if [ "$clients_response" = "200" ]; then
    clients_count=$(cat /tmp/clients_test.json | grep -o '"total":[0-9]*' | cut -d':' -f2)
    print_status 0 "Clients API working (Found $clients_count clients)"
else
    print_status 1 "Clients API failed (HTTP $clients_response)"
fi

# Test projects API
echo "Testing /api/projects..."
projects_response=$(curl -s -w "%{http_code}" -o /tmp/projects_test.json "http://localhost:9018/api/projects")
if [ "$projects_response" = "200" ]; then
    projects_count=$(cat /tmp/projects_test.json | grep -o '"total":[0-9]*' | cut -d':' -f2)
    print_status 0 "Projects API working (Found $projects_count projects)"
else
    print_status 1 "Projects API failed (HTTP $projects_response)"
fi

echo ""
print_info "4. Testing Project Creation..."

# Test project creation
create_response=$(curl -s -w "%{http_code}" -X POST "http://localhost:9018/api/projects" \
    -H "Content-Type: application/json" \
    -d '{
        "name":"VPS Test Project - '$(date +%s)'",
        "client_id":1,
        "description":"Automated test from VPS",
        "category":"website",
        "amount":15000,
        "contact_date":"2025-08-07",
        "status":"contacted"
    }' \
    -o /tmp/create_test.json)

if [ "$create_response" = "201" ]; then
    print_status 0 "Project creation successful"
    echo "Created project details:"
    cat /tmp/create_test.json | grep -o '"name":"[^"]*"' | head -1
else
    print_status 1 "Project creation failed (HTTP $create_response)"
    echo "Error details:"
    cat /tmp/create_test.json
fi

echo ""
print_info "5. Checking Database..."

# Check database connection
db_check=$(docker exec backend-mysql-1 mysql -u laravel -plaravel_password -D project_management -e "SELECT COUNT(*) as client_count FROM clients; SELECT COUNT(*) as project_count FROM projects;" 2>/dev/null)
if [ $? -eq 0 ]; then
    print_status 0 "Database connection working"
    echo "Database stats:"
    echo "$db_check"
else
    print_status 1 "Database connection failed"
fi

echo ""
print_info "6. Testing Frontend Access..."

# Test frontend accessibility
frontend_response=$(curl -s -w "%{http_code}" -o /dev/null "http://localhost:3000")
if [ "$frontend_response" = "200" ]; then
    print_status 0 "Frontend accessible"
else
    print_status 1 "Frontend not accessible (HTTP $frontend_response)"
fi

echo ""
print_info "7. Checking Service Logs (Last 10 lines)..."

echo "Backend App Logs:"
docker compose -f backend/docker-compose.yml logs --tail=10 app

echo ""
echo "Frontend Logs:"
docker compose -f frontend/docker-compose.yml logs --tail=10

echo ""
print_info "8. System Resource Usage..."
echo "Memory Usage:"
free -h
echo ""
echo "Disk Usage:"
df -h

echo ""
echo "=============================================="
print_info "Testing Complete!"
echo ""
echo "📋 Manual Testing Checklist:"
echo "   1. Access frontend: http://your-vps-ip:3000"
echo "   2. Navigate to /projects/create"
echo "   3. Fill form and submit"
echo "   4. Verify project appears in /projects list"
echo "   5. Test client management at /clients"
echo ""
echo "🔍 If issues found:"
echo "   - Check logs: docker compose logs [service-name]"
echo "   - Restart services: docker compose restart"
echo "   - Check .env configuration"
echo "   - Verify database migrations: docker exec backend-app-1 php artisan migrate:status"

# Cleanup temp files
rm -f /tmp/clients_test.json /tmp/projects_test.json /tmp/create_test.json