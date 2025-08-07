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
        $query = Project::with(['client', 'user']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $projects = $query->paginate($perPage);

        return response()->json($projects);
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
            'amount' => 'required|numeric|min:0',
            'contact_date' => 'required|date',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:contacted,in_progress,completed,paid',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        // Add user_id for current authenticated user (or default to 1 for now)
        $validated['user_id'] = auth()->id() ?? 1;
        $validated['is_active'] = true;

        $project = Project::create($validated);
        $project->load(['client', 'user']);

        return response()->json([
            'message' => '專案建立成功',
            'data' => $project
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
            'amount' => 'sometimes|numeric|min:0',
            'contact_date' => 'sometimes|date',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'status' => 'sometimes|in:contacted,in_progress,completed,paid',
            'priority' => 'nullable|in:low,medium,high',
        ]);

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
            'status' => 'required|in:contacted,in_progress,completed,paid'
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
            'paid_amount' => Project::where('status', 'paid')->sum('amount'),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}
