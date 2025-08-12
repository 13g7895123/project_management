<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check database connection
            \DB::connection()->getPdo();
            
            $query = Client::with(['contactMethods', 'projects']);
            
            // Add user filter if authenticated
            if (auth()->check()) {
                \Log::info('ClientController: User authenticated', [
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email ?? 'no email'
                ]);
                
                // Check if user has any clients
                $userClients = Client::where('user_id', auth()->id())->count();
                \Log::info('ClientController: User clients count', ['user_clients' => $userClients]);
                
                // If authenticated user has no clients, check if admin user has data
                if ($userClients === 0) {
                    $adminUser = \DB::table('users')->where('email', 'admin@project.mercylife.cc')->first();
                    if ($adminUser) {
                        $adminClients = Client::where('user_id', $adminUser->id)->count();
                        \Log::info('ClientController: Admin user clients count', ['admin_clients' => $adminClients]);
                        
                        // Temporarily show admin data if current user has no data
                        if ($adminClients > 0) {
                            \Log::info('ClientController: Showing admin data as fallback');
                            $query->where('user_id', $adminUser->id);
                        } else {
                            $query->where('user_id', auth()->id());
                        }
                    } else {
                        $query->where('user_id', auth()->id());
                    }
                } else {
                    $query->where('user_id', auth()->id());
                }
            } else {
                \Log::info('ClientController: User not authenticated, showing all clients');
                // For debugging: don't filter by user when not authenticated
            }
            
            // Debug: log total clients count before filtering
            $totalClients = \DB::table('clients')->count();
            \Log::info('ClientController: Total clients in database', ['count' => $totalClients]);

            // 搜尋功能
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('how_we_met', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            }

            // 篩選有效的業主
            if ($request->has('active')) {
                $query->where('is_active', $request->get('active'));
            }

            $clients = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));
            
            // Debug: log result count
            \Log::info('ClientController: Clients found after filtering', [
                'count' => $clients->count(),
                'total' => $clients->total()
            ]);

            // 加上專案數量
            $clients->getCollection()->transform(function ($client) {
                $client->projects_count = $client->projects()->count();
                return $client;
            });

            return response()->json([
                'success' => true,
                'data' => $clients,
                'message' => '業主列表獲取成功'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Database connection error in ClientController@index', [
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
            'how_we_met' => 'nullable|string',
            'notes' => 'nullable|string',
            'contacts' => 'array',
            'contacts.*.type' => 'required|string|in:phone,mobile,email,line,wechat,telegram,other',
            'contacts.*.value' => 'required|string|max:255',
            'contacts.*.is_primary' => 'boolean',
        ]);

        $client = Client::create([
            'user_id' => auth()->id() ?? 1, // Default to user 1 for development
            'name' => $validated['name'],
            'how_we_met' => $validated['how_we_met'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // 建立聯繫方式
        if (isset($validated['contacts'])) {
            foreach ($validated['contacts'] as $index => $contact) {
                $client->contactMethods()->create([
                    'type' => $contact['type'],
                    'value' => $contact['value'],
                    'is_primary' => $contact['is_primary'] ?? ($index === 0),
                ]);
            }
        }

        $client->load(['contactMethods', 'projects']);

        return response()->json([
            'success' => true,
            'data' => $client,
            'message' => '業主建立成功'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): JsonResponse
    {
        // 確保業主屬於當前用戶 (skip check in development if not authenticated)
        if (auth()->check() && $client->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '無權限存取此業主'
            ], 403);
        }

        $client->load(['contactMethods', 'projects']);
        $client->projects_count = $client->projects()->count();

        return response()->json([
            'success' => true,
            'data' => $client,
            'message' => '業主詳情獲取成功'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): JsonResponse
    {
        // 確保業主屬於當前用戶 (skip check in development if not authenticated)
        if (auth()->check() && $client->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '無權限修改此業主'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'how_we_met' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'contacts' => 'array',
            'contacts.*.id' => 'nullable|exists:contact_methods,id',
            'contacts.*.type' => 'required|string|in:phone,mobile,email,line,wechat,telegram,other',
            'contacts.*.value' => 'required|string|max:255',
            'contacts.*.is_primary' => 'boolean',
        ]);

        $client->update([
            'name' => $validated['name'],
            'how_we_met' => $validated['how_we_met'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['is_active'] ?? $client->is_active,
        ]);

        // 更新聯繫方式
        if (isset($validated['contacts'])) {
            // 刪除現有聯繫方式
            $client->contactMethods()->delete();
            
            // 建立新的聯繫方式
            foreach ($validated['contacts'] as $index => $contact) {
                $client->contactMethods()->create([
                    'type' => $contact['type'],
                    'value' => $contact['value'],
                    'is_primary' => $contact['is_primary'] ?? ($index === 0),
                ]);
            }
        }

        $client->load(['contactMethods', 'projects']);

        return response()->json([
            'success' => true,
            'data' => $client,
            'message' => '業主更新成功'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): JsonResponse
    {
        // 確保業主屬於當前用戶 (skip check in development if not authenticated)
        if (auth()->check() && $client->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '無權限刪除此業主'
            ], 403);
        }

        // 檢查是否有關聯的專案
        if ($client->projects()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => '此業主有關聯的專案，無法刪除'
            ], 422);
        }

        $client->delete();

        return response()->json([
            'success' => true,
            'message' => '業主刪除成功'
        ]);
    }

    /**
     * 獲取業主的專案列表
     */
    public function projects(Client $client, Request $request): JsonResponse
    {
        // 確保業主屬於當前用戶 (skip check in development if not authenticated)
        if (auth()->check() && $client->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '無權限存取此業主的專案'
            ], 403);
        }

        $projects = $client->projects()
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $projects,
            'message' => '業主專案列表獲取成功'
        ]);
    }

    /**
     * 獲取業主統計資訊
     */
    public function stats(Client $client): JsonResponse
    {
        // 確保業主屬於當前用戶 (skip check in development if not authenticated)
        if (auth()->check() && $client->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '無權限存取此業主統計'
            ], 403);
        }

        try {
            $stats = [
                'total_projects' => $client->projects()->count(),
                'completed_projects' => $client->projects()->where('status', 'completed')->count(),
                'active_projects' => $client->projects()->whereIn('status', ['pending', 'in_progress'])->count(),
                'total_revenue' => $client->projects()->where('status', 'completed')->sum('amount'),
                'pending_revenue' => $client->projects()->whereIn('status', ['pending', 'in_progress'])->sum('amount'),
                'avg_project_value' => $client->projects()->avg('amount'),
                'contact_methods_count' => $client->contactMethods()->count(),
                'first_project_date' => $client->projects()->oldest()->first()?->contact_date,
                'last_project_date' => $client->projects()->latest()->first()?->contact_date,
                'projects_by_status' => $client->projects()
                    ->selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'projects_by_category' => $client->projects()
                    ->selectRaw('category, count(*) as count, sum(amount) as revenue')
                    ->groupBy('category')
                    ->get()
                    ->keyBy('category')
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => '業主統計獲取成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '獲取業主統計失敗: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 新增聯繫方式
     */
    public function addContact(Client $client, Request $request): JsonResponse
    {
        // 確保業主屬於當前用戶
        if (auth()->check() && $client->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '無權限修改此業主'
            ], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:phone,mobile,email,line,wechat,telegram,other',
            'value' => 'required|string|max:255',
            'is_primary' => 'boolean',
        ]);

        // 如果設為主要聯繫方式，將其他設為非主要
        if ($validated['is_primary'] ?? false) {
            $client->contactMethods()->update(['is_primary' => false]);
        }

        $contact = $client->contactMethods()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $contact,
            'message' => '聯繫方式新增成功'
        ], 201);
    }

    /**
     * 更新聯繫方式
     */
    public function updateContact(Client $client, $contactId, Request $request): JsonResponse
    {
        // 確保業主屬於當前用戶
        if (auth()->check() && $client->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '無權限修改此業主'
            ], 403);
        }

        $contact = $client->contactMethods()->findOrFail($contactId);

        $validated = $request->validate([
            'type' => 'required|string|in:phone,mobile,email,line,wechat,telegram,other',
            'value' => 'required|string|max:255',
            'is_primary' => 'boolean',
        ]);

        // 如果設為主要聯繫方式，將其他設為非主要
        if ($validated['is_primary'] ?? false) {
            $client->contactMethods()->where('id', '!=', $contactId)->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'data' => $contact,
            'message' => '聯繫方式更新成功'
        ]);
    }

    /**
     * 刪除聯繫方式
     */
    public function deleteContact(Client $client, $contactId): JsonResponse
    {
        // 確保業主屬於當前用戶
        if (auth()->check() && $client->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '無權限修改此業主'
            ], 403);
        }

        $contact = $client->contactMethods()->findOrFail($contactId);
        $wasPrimary = $contact->is_primary;
        
        $contact->delete();

        // 如果刪除的是主要聯繫方式，將第一個設為主要
        if ($wasPrimary) {
            $client->contactMethods()->first()?->update(['is_primary' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => '聯繫方式刪除成功'
        ]);
    }
}