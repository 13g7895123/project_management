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

// Public routes (for development - add auth middleware as needed)
Route::group(['prefix' => '/'], function () {
    // Clients routes
    Route::get('clients/{client}/projects', [ClientController::class, 'projects']);
    Route::apiResource('clients', ClientController::class);

    // Projects routes - stats route must come before resource routes
    Route::get('projects/stats', [ProjectController::class, 'stats']);
    Route::put('projects/{project}/status', [ProjectController::class, 'updateStatus']);
    Route::apiResource('projects', ProjectController::class);

    // Dashboard routes
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/revenue', [DashboardController::class, 'revenue']);
    Route::get('dashboard/projects-timeline', [DashboardController::class, 'projectsTimeline']);
    Route::get('dashboard/activities', [DashboardController::class, 'activities']);
    Route::get('dashboard/revenue/trend', [DashboardController::class, 'revenueTrend']);
    Route::get('dashboard/projects/status-distribution', [DashboardController::class, 'statusDistribution']);
    Route::get('dashboard/revenue/by-category', [DashboardController::class, 'categoryBreakdown']);
});
