# API 404 Troubleshooting Guide

## Problem
Frontend APIs are returning 404 errors, especially on the `/projects/create` page.

## Root Cause Analysis
The issue is likely due to production environment differences where:
1. Frontend dev proxy doesn't work in production
2. Frontend trying to access wrong backend URL
3. Backend services not properly exposed
4. CORS configuration issues

## Solutions Implemented

### 1. Enhanced API Configuration (`frontend/src/composables/useApi.js`)
- ✅ Added intelligent URL detection for development vs production
- ✅ Environment-based backend URL configuration
- ✅ Fallback URL detection using current hostname + port 9018
- ✅ Enhanced error logging with detailed debugging information

### 2. Nuxt Runtime Configuration (`frontend/src/nuxt.config.ts`)
- ✅ Added multiple environment variables for backend configuration
- ✅ Support for `BACKEND_URL`, `BACKEND_HOST`, `BACKEND_PORT`
- ✅ Flexible configuration for different deployment scenarios

### 3. Docker Environment Configuration
- ✅ Updated `frontend/docker-compose.yml` with backend API environment variables
- ✅ Created `.env.example` with configuration templates
- ✅ Created `.env.production` with production-specific settings

### 4. Debugging Tools
- ✅ Created `debug-api-issues.sh` - comprehensive API debugging script
- ✅ Enhanced error messages with URL and method information
- ✅ Console logging for API requests in development

## How to Fix on VPS

### Step 1: Update Environment Configuration
```bash
# On your VPS, navigate to frontend directory
cd /home/jarvis/project/project_management/frontend

# Create or update .env file with your VPS IP
cat > .env << 'EOF'
FRONTEND_PORT=3000
BACKEND_URL=http://YOUR-VPS-IP:9018/api
BACKEND_HOST=YOUR-VPS-IP
BACKEND_PORT=9018
NUXT_PUBLIC_API_BASE_URL=http://YOUR-VPS-IP:9018/api
NUXT_PUBLIC_BACKEND_URL=http://YOUR-VPS-IP:9018/api
NUXT_PUBLIC_BACKEND_HOST=YOUR-VPS-IP
NUXT_PUBLIC_BACKEND_PORT=9018
EOF

# Replace YOUR-VPS-IP with actual IP address
sed -i 's/YOUR-VPS-IP/172.105.120.61/g' .env
```

### Step 2: Restart Services
```bash
# Restart frontend services
docker compose down
docker compose up -d --build

# Restart backend services (if needed)
cd ../backend
docker compose restart
```

### Step 3: Verify Configuration
```bash
# Run the debugging script
cd /home/jarvis/project/project_management
chmod +x debug-api-issues.sh
./debug-api-issues.sh
```

### Step 4: Test Manually
```bash
# Test backend directly
curl http://localhost:9018/api/clients

# Test through frontend (if port 3000 is accessible)
curl http://localhost:3000/api/clients

# Test from outside VPS (replace with your IP)
curl http://YOUR-VPS-IP:9018/api/clients
```

## Alternative Solutions

### Option 1: Use Nginx Reverse Proxy
Set up Nginx to proxy `/api` requests to backend:

```nginx
# In nginx.conf
location /api/ {
    proxy_pass http://localhost:9018/api/;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
}
```

### Option 2: Docker Network Configuration
If both services are in Docker, use Docker networks:

```yaml
# docker-compose.yml
networks:
  app-network:
    driver: bridge

services:
  frontend:
    networks:
      - app-network
    environment:
      - BACKEND_URL=http://backend:9018/api
  
  backend:
    networks:
      - app-network
```

## Testing Checklist

- [ ] Backend services running: `docker compose ps` in backend directory
- [ ] Frontend services running: `docker compose ps` in frontend directory  
- [ ] Backend API accessible: `curl http://localhost:9018/api/clients`
- [ ] Frontend can reach backend: Check browser console for API errors
- [ ] Environment variables set correctly: Check `.env` files
- [ ] Ports are open: `netstat -tlnp | grep 9018`
- [ ] CORS allows frontend requests: Check `backend/src/config/cors.php`

## Common Error Messages & Solutions

### "404 Not Found"
- Backend routes not properly registered
- Wrong API URL in frontend configuration
- Backend service not running

### "CORS Error" 
- Update `backend/src/config/cors.php` to allow frontend origin
- Ensure `Access-Control-Allow-Origin` header is set

### "Connection Refused"
- Backend service not running or accessible
- Wrong port or host configuration
- Firewall blocking connections

### "Timeout"
- Backend service taking too long to respond
- Network connectivity issues
- Resource constraints (CPU/Memory)

## Debug Commands

```bash
# Check service status
docker compose ps

# View logs
docker compose logs app

# Test API endpoints
curl -v http://localhost:9018/api/clients

# Check network connectivity
ping localhost
telnet localhost 9018

# Check environment variables in container
docker exec frontend-app-1 env | grep BACKEND
```

## Prevention
1. Always test in production-like environment before deployment
2. Use environment-specific configuration files
3. Implement health check endpoints
4. Set up monitoring and logging
5. Document all configuration requirements