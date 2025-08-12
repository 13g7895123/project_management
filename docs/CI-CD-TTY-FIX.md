# CI/CD TTY Error Fix

## Problem Description
Point 47 from PROMPT.md: CI/CD deployment fails with error:
```
the input device is not a TTY
Error: Process completed with exit code 1.
```

## Root Cause
The error occurs when using `docker-compose exec` in a non-interactive environment (like GitHub Actions). The `exec` command by default tries to allocate a TTY (pseudo-terminal), but CI/CD environments don't provide one.

## Solution Applied

### 1. Fixed GitHub Actions Workflow
Updated `.github/workflows/deploy.yml`:

**Before (Problematic):**
```yaml
docker-compose exec backend php artisan migrate --force
```

**After (Fixed):**
```yaml
# Use -T flag to disable TTY allocation for non-interactive commands
docker-compose exec -T backend php artisan migrate --force
```

### 2. Enhanced Deployment Process
Added comprehensive deployment steps:

```yaml
- Set error handling with `set -e`
- Added container startup wait time
- Improved error handling with fallback messages
- Added deployment verification steps
- Enhanced logging for better debugging
```

### 3. Created Standalone Deployment Script
Added `scripts/deploy.sh` for local and CI/CD use:

```bash
# Usage examples:
./scripts/deploy.sh              # Full deployment with build
./scripts/deploy.sh --no-build   # Skip rebuild
./scripts/deploy.sh --no-cache   # Skip cache operations
```

## Key Changes Made

### A. Docker Compose Commands
All `docker-compose exec` commands now use the `-T` flag:
- `docker-compose exec -T backend php artisan migrate --force`
- `docker-compose exec -T backend php artisan db:seed --force`
- `docker-compose exec -T backend php artisan config:cache`

### B. Error Handling
Added graceful error handling:
```bash
command || echo "Fallback message if command fails"
```

### C. Better Logging
Added descriptive echo statements to track deployment progress.

## What the `-T` Flag Does

- `-T`: Disable pseudo-TTY allocation
- Prevents the "input device is not a TTY" error
- Allows commands to run in non-interactive environments
- Essential for CI/CD pipelines

## Verification

After applying the fix, the CI/CD pipeline should:

1. ✅ Complete without TTY errors
2. ✅ Successfully run database migrations
3. ✅ Apply database seeders
4. ✅ Cache application configurations
5. ✅ Report deployment status

## Alternative Solutions (if needed)

If the `-T` flag doesn't work in some environments:

### Option 1: Use `docker-compose run`
```bash
docker-compose run --rm backend php artisan migrate --force
```

### Option 2: Use shell script inside container
```bash
docker-compose exec backend /bin/bash -c "php artisan migrate --force"
```

### Option 3: Conditional TTY allocation
```bash
if [ -t 1 ]; then
    docker-compose exec backend php artisan migrate --force
else
    docker-compose exec -T backend php artisan migrate --force
fi
```

## Testing the Fix

### Local Testing:
```bash
# Test the deployment script locally
./scripts/deploy.sh

# Test specific commands
docker-compose exec -T backend php artisan migrate:status
```

### CI/CD Testing:
1. Commit and push changes
2. Monitor GitHub Actions workflow
3. Check deployment logs for success messages

## Expected Results

After the fix, the deployment should:
- Complete without TTY-related errors
- Successfully set up the database
- Make the application available at the configured domain
- Provide admin access with: `admin@example.com` / `password`