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
                $query->where('user_id', auth()->id());
            }

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
}