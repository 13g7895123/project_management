<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DashboardController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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

// Public routes (for development - add auth middleware as needed)
Route::group(['prefix' => '/'], function () {
    // Clients routes
    Route::get('clients/{client}/projects', [ClientController::class, 'projects']);
    Route::get('clients/{client}/stats', [ClientController::class, 'stats']);
    Route::post('clients/{client}/contacts', [ClientController::class, 'addContact']);
    Route::put('clients/{client}/contacts/{contact}', [ClientController::class, 'updateContact']);
    Route::delete('clients/{client}/contacts/{contact}', [ClientController::class, 'deleteContact']);
    Route::apiResource('clients', ClientController::class);

    // Projects routes - specific routes must come before resource routes
    Route::get('projects/stats', [ProjectController::class, 'stats']);
    Route::get('projects/category/{category}', [ProjectController::class, 'byCategory']);
    Route::get('projects/status/{status}', [ProjectController::class, 'byStatus']);
    Route::get('projects/export/{format}', [ProjectController::class, 'export']);
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
});
