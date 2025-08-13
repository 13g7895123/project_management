<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check database connection
            \DB::connection()->getPdo();
            
            $query = Project::with(['client', 'user']);
            
            // Debug: log total projects count before any filtering
            $totalProjects = \DB::table('projects')->count();
            \Log::info('ProjectController: Total projects in database', ['count' => $totalProjects]);
            
            // Check if user is authenticated
            if (auth()->check()) {
                $user = auth()->user();
                \Log::info('ProjectController: User authenticated', [
                    'user_id' => $user->id,
                    'user_email' => $user->email ?? 'no email',
                    'user_role' => $user->role ?? 'no role',
                    'user_attributes' => $user->getAttributes()
                ]);
                
                // Check how many projects exist for this user
                $userProjectCount = Project::where('user_id', $user->id)->count();
                \Log::info('ProjectController: Projects for current user', ['count' => $userProjectCount]);
                
                // Check projects for all users to understand data distribution
                $allProjectsGrouped = \DB::table('projects')
                    ->select('user_id', \DB::raw('count(*) as count'))
                    ->groupBy('user_id')
                    ->get();
                \Log::info('ProjectController: Projects by user_id', ['distribution' => $allProjectsGrouped]);
                
                // Role-based filtering: Admin sees all, users see only their own
                if ($user->role === 'admin' || $user->isAdmin()) {
                    \Log::info('ProjectController: Admin user - showing all projects');
                    // Admin can see all projects, no filtering needed
                } else {
                    $query->where('user_id', $user->id);
                    \Log::info('ProjectController: Regular user - filtering by user_id', ['user_id' => $user->id]);
                }
            } else {
                \Log::info('ProjectController: User not authenticated - returning empty result for security');
                // If not authenticated, return empty result for security
                $query->whereRaw('1 = 0'); // This will return no results
            }

            // Apply filters - only when values are not empty
            if ($request->filled('status')) {
                $query->where('status', $request->status);
                \Log::info('ProjectController: Applied status filter', ['status' => $request->status]);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
                \Log::info('ProjectController: Applied category filter', ['category' => $request->category]);
            }

            if ($request->filled('client_id')) {
                $query->where('client_id', $request->client_id);
                \Log::info('ProjectController: Applied client_id filter', ['client_id' => $request->client_id]);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
                \Log::info('ProjectController: Applied search filter', ['search' => $search]);
            }

            // Log when no filters are applied
            $filtersApplied = $request->filled('status') || $request->filled('category') || $request->filled('client_id') || $request->filled('search');
            \Log::info('ProjectController: Filters summary', [
                'filters_applied' => $filtersApplied,
                'status_filter' => $request->filled('status') ? $request->status : 'not applied',
                'category_filter' => $request->filled('category') ? $request->category : 'not applied',
                'client_id_filter' => $request->filled('client_id') ? $request->client_id : 'not applied',
                'search_filter' => $request->filled('search') ? $request->search : 'not applied'
            ]);

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Debug: log the actual SQL query being executed
            \Log::info('ProjectController: Final SQL query', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $projects = $query->paginate($perPage);
            
            // Debug: log result count and first few items
            \Log::info('ProjectController: Projects found after filtering', [
                'count' => $projects->count(),
                'total' => $projects->total(),
                'per_page' => $projects->perPage(),
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'items_sample' => $projects->items() ? array_slice($projects->items(), 0, 2) : []
            ]);

            return response()->json([
                'success' => true,
                'data' => $projects,
                'message' => '專案列表獲取成功'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Database connection error in ProjectController@index', [
                'error' => $e->getMessage(),
                'db_host' => config('database.connections.mysql.host'),
                'db_database' => config('database.connections.mysql.database')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'error' => [
                    'type' => 'database_error',
                    'details' => config('app.debug') ? $e->getMessage() : 'Internal server error'
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'category' => 'required|in:website,script,server,custom',
            'amount' => 'nullable|numeric|min:0',
            'contact_date' => 'required|date',
            'start_date' => 'nullable|date',
            'expected_completion_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:pending_evaluation,contacted,in_progress,completed,paid',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        // Make amount required for all statuses except pending_evaluation
        if ($validated['status'] !== 'pending_evaluation') {
            $request->validate([
                'amount' => 'required|numeric|min:0',
            ]);
        }

        // Add user_id for current authenticated user (or default to 1 for now)
        $validated['user_id'] = auth()->id() ?? 1;
        $validated['is_active'] = true;

        $project = Project::create($validated);
        $project->load(['client', 'user']);

        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => '專案建立成功'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $project = Project::with(['client', 'user'])->findOrFail($id);

        return response()->json([
            'data' => $project
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'client_id' => 'sometimes|exists:clients,id',
            'description' => 'nullable|string',
            'category' => 'sometimes|in:website,script,server,custom',
            'amount' => 'nullable|numeric|min:0',
            'contact_date' => 'sometimes|date',
            'start_date' => 'nullable|date',
            'expected_completion_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'status' => 'sometimes|in:pending_evaluation,contacted,in_progress,completed,paid',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        // Make amount required for all statuses except pending_evaluation
        if (isset($validated['status']) && $validated['status'] !== 'pending_evaluation') {
            $request->validate([
                'amount' => 'required|numeric|min:0',
            ]);
        } elseif (!isset($validated['status']) && $project->status !== 'pending_evaluation') {
            // If status is not being updated but current status requires amount
            if (isset($validated['amount'])) {
                $request->validate([
                    'amount' => 'required|numeric|min:0',
                ]);
            }
        }

        $project->update($validated);
        $project->load(['client', 'user']);

        return response()->json([
            'message' => '專案更新成功',
            'data' => $project
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'message' => '專案刪除成功'
        ]);
    }

    /**
     * Update project status
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending_evaluation,contacted,in_progress,completed,paid'
        ]);

        $project->update($validated);
        $project->load(['client', 'user']);

        return response()->json([
            'message' => '專案狀態更新成功',
            'data' => $project
        ]);
    }

    /**
     * Get project statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_projects' => Project::count(),
            'by_status' => Project::groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->pluck('count', 'status'),
            'by_category' => Project::groupBy('category')
                ->selectRaw('category, count(*) as count')
                ->pluck('count', 'category'),
            'total_amount' => Project::sum('amount'),
            'paid_amount' => Project::where('status', 'completed')->sum('amount'),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }

    /**
     * Get projects by category
     */
    public function byCategory(string $category, Request $request): JsonResponse
    {
        $query = Project::with(['client', 'user'])->where('category', $category);
        
        // Apply user filtering based on role
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }
        } else {
            $query->whereRaw('1 = 0'); // Return no results if not authenticated
        }
        
        // Apply additional filters - only when values are not empty
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $projects = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $projects,
            'message' => "類別 {$category} 的專案列表"
        ]);
    }

    /**
     * Get projects by status
     */
    public function byStatus(string $status, Request $request): JsonResponse
    {
        $query = Project::with(['client', 'user'])->where('status', $status);
        
        // Apply user filtering based on role
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }
        } else {
            $query->whereRaw('1 = 0'); // Return no results if not authenticated
        }
        
        // Apply additional filters - only when values are not empty
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $projects = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $projects,
            'message' => "狀態 {$status} 的專案列表"
        ]);
    }

    /**
     * Export projects data
     */
    public function export(string $format, Request $request): JsonResponse
    {
        // Note: In a real application, you would implement actual export functionality
        // For now, return a placeholder response
        
        $query = Project::with(['client', 'user']);
        
        // Apply user filtering based on role
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }
        } else {
            $query->whereRaw('1 = 0'); // Return no results if not authenticated
        }
        
        // Apply filters if provided - only when values are not empty
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $projects = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'format' => $format,
                'total_records' => $projects->count(),
                'download_url' => "/api/projects/export/{$format}?token=" . uniqid(),
                'expires_at' => now()->addMinutes(30),
            ],
            'message' => "專案資料匯出準備完成 ({$format} 格式)"
        ]);
    }

    /**
     * Get project milestones (placeholder)
     */
    public function milestones(string $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        
        // Note: This would require a milestones table/model in a full implementation
        // For now, return a placeholder response
        
        return response()->json([
            'success' => true,
            'data' => [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'milestones' => [] // Placeholder - would contain actual milestones
            ],
            'message' => '專案里程碑列表'
        ]);
    }

    /**
     * Create project milestone (placeholder)
     */
    public function createMilestone(string $projectId, Request $request): JsonResponse
    {
        $project = Project::findOrFail($projectId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'completed' => 'boolean'
        ]);
        
        // Note: This would create an actual milestone in a full implementation
        
        return response()->json([
            'success' => true,
            'data' => array_merge(['id' => rand(1000, 9999)], $validated),
            'message' => '專案里程碑創建成功'
        ], 201);
    }

    /**
     * Update project milestone (placeholder)
     */
    public function updateMilestone(string $projectId, string $milestoneId, Request $request): JsonResponse
    {
        $project = Project::findOrFail($projectId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'completed' => 'boolean'
        ]);
        
        return response()->json([
            'success' => true,
            'data' => array_merge(['id' => $milestoneId], $validated),
            'message' => '專案里程碑更新成功'
        ]);
    }

    /**
     * Delete project milestone (placeholder)
     */
    public function deleteMilestone(string $projectId, string $milestoneId): JsonResponse
    {
        $project = Project::findOrFail($projectId);
        
        return response()->json([
            'success' => true,
            'message' => '專案里程碑刪除成功'
        ]);
    }
}
