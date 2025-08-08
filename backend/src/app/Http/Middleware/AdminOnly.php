<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
                'error' => 'Unauthenticated'
            ], 401);
        }

        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Administrator privileges required.',
                'error' => 'Forbidden'
            ], 403);
        }

        // Check if user is active
        if (!$request->user()->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Account is not active.',
                'error' => 'Account Inactive'
            ], 403);
        }

        return $next($request);
    }
}