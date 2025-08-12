# Server Error Fix - Database Connection Issue

## Problem Description
Point 46 from PROMPT.md: The API is now calling the correct domain (`https://project.mercylife.cc/api/auth/login`) but returning "Server Error" (HTTP 500).

## Root Cause Analysis
The API endpoints that don't require database access work fine (`/api/test`), but authentication endpoints that need database access fail with a 500 server error. The health endpoint shows:

```json
{
  "database": "error: SQLSTATE[HY000] [2002] Connection refused"
}
```

This indicates that the Laravel backend cannot connect to the MySQL database.

## Why This Happens
1. **Database Container Not Running**: The MySQL database container may not be running or accessible
2. **Network Configuration**: The backend is trying to connect to `mysql` hostname but the database isn't reachable
3. **Missing Migrations/Seeders**: Even if connected, the database tables may not exist

## Solutions

### Option 1: Check Database Container Status
```bash
# Check if database is running
docker ps | grep mysql

# If not running, start the full stack
docker-compose up -d
```

### Option 2: Manual Database Setup
If the database container is running but tables are missing:

```bash
# Access backend container
docker exec -it <backend-container-name> bash

# Run migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# Verify setup
php artisan migrate:status
```

### Option 3: Use Database Setup Command
We've added a custom artisan command for easier database setup:

```bash
docker exec <backend-container-name> php artisan setup:database --force
```

### Option 4: Automated Setup Route (Temporary)
For debugging, access the database setup route:
```
GET https://project.mercylife.cc/api/database/setup
```

## Expected Result After Fix
Once the database connection is established and tables are created with seeders:

1. **Login should work** with these credentials:
   - Email: `admin@example.com`  
   - Password: `password`

2. **API Response** should be:
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": { ... },
        "token": "..."
    }
}
```

## Files Modified
- `backend/entrypoint.sh` - Improved database connection handling
- `backend/src/app/Http/Controllers/Api/AuthController.php` - Added debug info
- `backend/src/routes/api.php` - Added database setup endpoint
- `backend/src/app/Console/Commands/SetupDatabase.php` - New setup command

## Verification Steps
1. Test basic API: `curl https://project.mercylife.cc/api/test`
2. Check database health: `curl https://project.mercylife.cc/api/health` 
3. Try login: 
```bash
curl -X POST https://project.mercylife.cc/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"login":"admin@example.com","password":"password"}'
```

## Next Steps
After confirming the database connection issue is resolved, the API should work correctly and the server error will be fixed.