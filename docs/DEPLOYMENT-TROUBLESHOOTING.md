# VPS Deployment Testing & Troubleshooting Guide

## Quick Testing Steps

### 1. SSH into VPS and Run Test Script
```bash
# SSH into your VPS
ssh -p 8022 jarvis@172.105.120.61

# Navigate to project and run test script
cd /home/jarvis/project/project_management
chmod +x test-deployment.sh
./test-deployment.sh
```

### 2. Manual Verification Steps

#### Check Services Status
```bash
# Backend services
cd backend && docker compose ps
cd ../frontend && docker compose ps
```

#### Test API Directly
```bash
# Test clients API
curl http://localhost:9018/api/clients

# Test project creation
curl -X POST http://localhost:9018/api/projects \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","client_id":1,"category":"website","amount":10000,"contact_date":"2025-08-07","status":"contacted"}'
```

#### Access Frontend
- Browser: `http://your-vps-ip:3000`
- Test project creation form: `/projects/create`

## Common Issues & Solutions

### Issue 1: Services Not Running
```bash
# Restart services
cd backend && docker compose restart
cd ../frontend && docker compose restart
```

### Issue 2: Database Not Updated
```bash
# Run migrations and seeders
docker exec backend-app-1 php artisan migrate:fresh --seed
```

### Issue 3: API Not Responding
```bash
# Check backend logs
docker compose -f backend/docker-compose.yml logs app

# Check if database is connected
docker exec backend-mysql-1 mysql -u laravel -plaravel_password -e "SHOW DATABASES;"
```

### Issue 4: Frontend Can't Connect to Backend
Check if proxy is working in production. May need to update `nuxt.config.ts` for production environment.

### Issue 5: Port Conflicts
```bash
# Check what's using the ports
netstat -tulpn | grep :9018
netstat -tulpn | grep :3000
```

## Environment-Specific Commands

### Update from Git
```bash
cd /home/jarvis/project/project_management
git pull origin master
docker compose -f backend/docker-compose.yml down
docker compose -f frontend/docker-compose.yml down
docker compose -f backend/docker-compose.yml up -d
docker compose -f frontend/docker-compose.yml up -d
```

### Database Operations
```bash
# Check migration status
docker exec backend-app-1 php artisan migrate:status

# Run fresh migrations with seed data
docker exec backend-app-1 php artisan migrate:fresh --seed

# Access database directly
docker exec -it backend-mysql-1 mysql -u laravel -plaravel_password project_management
```

### Logs Investigation
```bash
# Backend application logs
docker logs backend-app-1

# Database logs
docker logs backend-mysql-1

# Frontend logs
docker logs frontend-app-1

# Nginx logs (if using)
docker logs backend-nginx-1
```

## Performance Testing

### Load Testing API
```bash
# Install ab (Apache Bench) if not available
sudo apt-get install apache2-utils

# Test API performance
ab -n 100 -c 10 http://localhost:9018/api/clients
```

### Monitor Resources
```bash
# Monitor Docker containers
docker stats

# System resources
htop
df -h
free -h
```

## Security Checklist

- [ ] Only necessary ports are exposed
- [ ] Database password is secure
- [ ] .env files contain proper production values
- [ ] SSL/TLS configured (if using HTTPS)
- [ ] Firewall rules are properly configured

## Maintenance Commands

### Cleanup
```bash
# Remove unused Docker images
docker image prune -a

# Remove unused volumes
docker volume prune
```

### Backup
```bash
# Backup database
docker exec backend-mysql-1 mysqldump -u laravel -plaravel_password project_management > backup_$(date +%Y%m%d).sql
```

### Update Dependencies
```bash
# Backend
cd backend/src
composer update

# Frontend  
cd ../../frontend/src
npm update
```