#!/bin/bash

echo "🔐 Authentication Testing Script"
echo "================================"

API_URL="https://project.mercylife.cc/api"

echo "Testing various login methods..."
echo ""

# Test with email
echo "1. Testing login with EMAIL: admin@example.com"
response1=$(curl -X POST ${API_URL}/auth/login \
    -H "Content-Type: application/json" \
    -d '{"login":"admin@example.com","password":"password"}' \
    -s)

if echo "$response1" | grep -q '"success":true'; then
    echo "✅ Email login: SUCCESS"
    token1=$(echo "$response1" | jq -r '.data.token' 2>/dev/null)
else
    echo "❌ Email login: FAILED"
    echo "Response: $response1"
fi

echo ""

# Test with username  
echo "2. Testing login with USERNAME: admin"
response2=$(curl -X POST ${API_URL}/auth/login \
    -H "Content-Type: application/json" \
    -d '{"login":"admin","password":"password"}' \
    -s)

if echo "$response2" | grep -q '"success":true'; then
    echo "✅ Username login: SUCCESS" 
    token2=$(echo "$response2" | jq -r '.data.token' 2>/dev/null)
else
    echo "❌ Username login: FAILED"
    echo "Response: $response2"
fi

echo ""

# Test invalid credentials
echo "3. Testing INVALID credentials"
response3=$(curl -X POST ${API_URL}/auth/login \
    -H "Content-Type: application/json" \
    -d '{"login":"admin","password":"wrongpassword"}' \
    -s)

if echo "$response3" | grep -q '"success":false'; then
    echo "✅ Invalid credentials properly rejected"
else
    echo "❌ Invalid credentials test failed"
    echo "Response: $response3"
fi

echo ""

# Test authenticated endpoint if we have a token
if [ ! -z "$token1" ]; then
    echo "4. Testing authenticated endpoint with token"
    me_response=$(curl -X GET ${API_URL}/auth/me \
        -H "Authorization: Bearer $token1" \
        -H "Content-Type: application/json" \
        -s)
    
    if echo "$me_response" | grep -q '"success":true'; then
        echo "✅ Authenticated endpoint: SUCCESS"
        echo "User info: $(echo "$me_response" | jq '.data.user.name' 2>/dev/null)"
    else
        echo "❌ Authenticated endpoint: FAILED"
        echo "Response: $me_response"
    fi
fi

echo ""
echo "🔍 Database Health Check"
health_response=$(curl -X GET ${API_URL}/health -s)
echo "Database status: $(echo "$health_response" | jq '.database' 2>/dev/null)"
echo "Total users: $(echo "$health_response" | jq '.clients_count' 2>/dev/null)"

echo ""
echo "📋 Summary of Working Credentials:"
echo "================================="
echo "Email:    admin@example.com"
echo "Username: admin" 
echo "Password: password"
echo "Role:     admin"
echo ""
echo "Additional admin users (from seeder):"
echo "- zhiming@admin.com / password (username: zhiming.admin)"
echo ""
echo "Regular users (from seeder):"  
echo "- xiaoming@freelancer.tw / password (username: xiaoming.wang)"
echo "- meihua@design.com.tw / password (username: meihua.chen)"
echo "- jianguo@webdev.tw / password (username: jianguo.li)"