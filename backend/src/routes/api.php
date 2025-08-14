<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebsiteSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes (public)
Route::post('auth/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::put('auth/change-password', [AuthController::class, 'changePassword']);
    
    // Legacy user endpoint for compatibility
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Health check and test routes
Route::get('health', function () {
    try {
        // Check database connection
        $dbStatus = 'connected';
        $clientsCount = 0;
        
        try {
            \DB::connection()->getPdo();
            $clientsCount = \DB::table('clients')->count();
        } catch (\Exception $e) {
            $dbStatus = 'error: ' . $e->getMessage();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'API is healthy!',
            'timestamp' => now(),
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'database' => $dbStatus,
            'clients_count' => $clientsCount,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'API health check failed',
            'error' => $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

Route::get('test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API test endpoint is working!',
        'timestamp' => now(),
        'app_name' => config('app.name')
    ]);
});

Route::get('test/projects', function () {
    try {
        // Test database connectivity and get projects data
        $projects = \DB::table('projects')->get();
        $projectsCount = $projects->count();
        
        // Get user distribution
        $userDistribution = \DB::table('projects')
            ->select('user_id', \DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->get();
            
        // Get users info
        $users = \DB::table('users')->select('id', 'name', 'email', 'role')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Projects test data retrieved successfully',
            'data' => [
                'total_projects' => $projectsCount,
                'user_distribution' => $userDistribution,
                'users' => $users,
                'sample_projects' => $projects->take(3),
                'timestamp' => now()
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Projects test failed',
            'error' => $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

Route::get('test/projects/auth-test', function () {
    try {
        // Temporary test route to check projects without authentication
        $projectController = new \App\Http\Controllers\Api\ProjectController();
        
        // Create a mock request
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'search' => '',
            'category' => '',
            'status' => ''
        ]);
        
        // Call the index method directly
        $response = $projectController->index($request);
        
        return response()->json([
            'success' => true,
            'message' => 'Direct controller test',
            'response_data' => $response->getData(),
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Direct controller test failed',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'timestamp' => now()
        ], 500);
    }
});

Route::get('database/setup', function () {
    try {
        // Check database connection
        \DB::connection()->getPdo();
        
        // Run migrations if needed
        $migrationOutput = [];
        try {
            \Artisan::call('migrate', ['--force' => true]);
            $migrationOutput[] = 'Migrations completed';
        } catch (\Exception $e) {
            $migrationOutput[] = 'Migration error: ' . $e->getMessage();
        }
        
        // Run seeders if needed
        $seedOutput = [];
        try {
            \Artisan::call('db:seed', ['--force' => true]);
            $seedOutput[] = 'Seeders completed';
        } catch (\Exception $e) {
            $seedOutput[] = 'Seeder error: ' . $e->getMessage();
        }
        
        // Get database stats
        $stats = [];
        try {
            $stats['users'] = \DB::table('users')->count();
            $stats['clients'] = \DB::table('clients')->count();  
            $stats['projects'] = \DB::table('projects')->count();
            $stats['admin_user'] = \DB::table('users')->where('email', 'admin@example.com')->exists();
        } catch (\Exception $e) {
            $stats['error'] = $e->getMessage();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Database setup completed',
            'migration_output' => $migrationOutput,
            'seed_output' => $seedOutput,
            'database_stats' => $stats,
            'timestamp' => now()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Database setup failed',
            'error' => $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// Protected admin routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Profile routes
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('profile/avatar', [ProfileController::class, 'removeAvatar']);
    Route::put('profile/password', [ProfileController::class, 'changePassword']);
    Route::delete('profile', [ProfileController::class, 'deleteAccount']);
    
    // Clients routes
    Route::get('clients/{client}/projects', [ClientController::class, 'projects']);
    Route::get('clients/{client}/stats', [ClientController::class, 'stats']);
    Route::post('clients/{client}/contacts', [ClientController::class, 'addContact']);
    Route::put('clients/{client}/contacts/{contact}', [ClientController::class, 'updateContact']);
    Route::delete('clients/{client}/contacts/{contact}', [ClientController::class, 'deleteContact']);
    Route::post('clients/import', [ClientController::class, 'import']); // Import clients
    Route::apiResource('clients', ClientController::class);

    // Projects routes - specific routes must come before resource routes
    Route::get('projects/stats', [ProjectController::class, 'stats']);
    Route::get('projects/category/{category}', [ProjectController::class, 'byCategory']);
    Route::get('projects/status/{status}', [ProjectController::class, 'byStatus']);
    Route::get('projects/export/{format}', [ProjectController::class, 'export']);
    Route::post('projects/import', [ProjectController::class, 'import']); // Import projects
    Route::put('projects/{project}/status', [ProjectController::class, 'updateStatus']);
    Route::get('projects/{project}/milestones', [ProjectController::class, 'milestones']);
    Route::post('projects/{project}/milestones', [ProjectController::class, 'createMilestone']);
    Route::put('projects/{project}/milestones/{milestone}', [ProjectController::class, 'updateMilestone']);
    Route::delete('projects/{project}/milestones/{milestone}', [ProjectController::class, 'deleteMilestone']);
    Route::apiResource('projects', ProjectController::class);

    // Dashboard routes
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/clients', [DashboardController::class, 'clients']);
    Route::get('dashboard/clients/top-revenue', [DashboardController::class, 'topClients']);
    Route::get('dashboard/activities', [DashboardController::class, 'activities']);
    Route::get('dashboard/deadlines', [DashboardController::class, 'deadlines']);
    Route::get('dashboard/revenue/trend', [DashboardController::class, 'revenueTrend']);
    Route::get('dashboard/revenue/by-category', [DashboardController::class, 'categoryBreakdown']);
    Route::get('dashboard/revenue/{period}', [DashboardController::class, 'revenueByPeriod']);
    Route::get('dashboard/projects/status-distribution', [DashboardController::class, 'statusDistribution']);
    Route::get('dashboard/projects/{period}', [DashboardController::class, 'projectsByPeriod']);
    Route::get('dashboard/daily-stats', [DashboardController::class, 'dailyStats']);
    Route::get('dashboard/weekly-stats', [DashboardController::class, 'weeklyStats']);
    Route::get('dashboard/yearly-stats', [DashboardController::class, 'yearlyStats']);
    Route::get('dashboard/projects-timeline', [DashboardController::class, 'projectsTimeline']);

    // User Management routes (Admin only) - using admin middleware for extra security
    Route::prefix('users')->middleware('admin')->group(function () {
        Route::get('/', [UserController::class, 'index']); // List users with pagination/filtering
        Route::post('/', [UserController::class, 'store']); // Create new user
        Route::post('/import', [UserController::class, 'import']); // Import users
        Route::get('/{id}', [UserController::class, 'show']); // Show specific user
        Route::put('/{id}', [UserController::class, 'update']); // Update user
        Route::delete('/{id}', [UserController::class, 'destroy']); // Soft delete user
        Route::patch('/{id}/status', [UserController::class, 'toggleStatus']); // Toggle user status
        Route::patch('/{id}/password', [UserController::class, 'changePassword']); // Change user password
        Route::patch('/{id}/restore', [UserController::class, 'restore']); // Restore soft deleted user
        Route::delete('/{id}/force', [UserController::class, 'forceDelete']); // Permanently delete user
    });

    // Website Settings routes (Admin only)
    Route::prefix('website-settings')->middleware('admin')->group(function () {
        Route::get('/', [WebsiteSettingsController::class, 'index']); // Get all settings
        Route::post('/', [WebsiteSettingsController::class, 'update']); // Update settings
        Route::get('/{key}', [WebsiteSettingsController::class, 'show']); // Get specific setting
        Route::post('/reset-defaults', [WebsiteSettingsController::class, 'resetDefaults']); // Reset to defaults
    });
});

