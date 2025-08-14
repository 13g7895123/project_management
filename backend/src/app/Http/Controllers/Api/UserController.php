<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class UserController extends Controller
{
    public function __construct()
    {
        // All user management operations require admin role
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     * Display a listing of users with pagination, search, and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $users = QueryBuilder::for(User::class)
                ->allowedFilters([
                    'name',
                    'email',
                    'username',
                    AllowedFilter::exact('role'),
                    AllowedFilter::exact('status'),
                    AllowedFilter::scope('active'),
                    AllowedFilter::scope('admins'),
                    AllowedFilter::scope('users'),
                ])
                ->allowedSorts([
                    'name',
                    'email',
                    'username',
                    'role',
                    'status',
                    'created_at',
                    'updated_at',
                    'last_login_at'
                ])
                ->defaultSort('-created_at')
                ->paginate($request->get('per_page', 15))
                ->withQueryString();

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
                'meta' => [
                    'total_users' => User::count(),
                    'active_users' => User::active()->count(),
                    'admin_users' => User::admins()->count(),
                    'available_roles' => User::getRoles(),
                    'available_statuses' => User::getStatuses(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'nullable|string|max:255|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string|max:1000',
                'website' => 'nullable|url|max:255',
                'location' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'position' => 'nullable|string|max:255',
                'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
                'status' => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_SUSPENDED])],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $validator->validated();
            $userData['password'] = Hash::make($userData['password']);
            $userData['email_verified_at'] = now();

            $user = User::create($userData);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => $user->fresh()
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
                'meta' => [
                    'is_deleted' => $user->trashed(),
                    'role_label' => User::getRoles()[$user->role] ?? $user->role,
                    'status_label' => User::getStatuses()[$user->status] ?? $user->status,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'error' => config('app.debug') ? $e->getMessage() : 'User not found'
            ], 404);
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent self-modification of role/status for security
            $currentUser = $request->user();
            $isModifyingSelf = $currentUser->id == $user->id;

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'username' => [
                    'sometimes',
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('users', 'username')->ignore($user->id)
                ],
                'email' => [
                    'sometimes',
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id)
                ],
                'phone' => 'sometimes|nullable|string|max:20',
                'bio' => 'sometimes|nullable|string|max:1000',
                'website' => 'sometimes|nullable|url|max:255',
                'location' => 'sometimes|nullable|string|max:255',
                'company' => 'sometimes|nullable|string|max:255',
                'position' => 'sometimes|nullable|string|max:255',
                'role' => [
                    'sometimes',
                    'required',
                    Rule::in([User::ROLE_ADMIN, User::ROLE_USER]),
                    // Prevent self role modification
                    function ($attribute, $value, $fail) use ($isModifyingSelf) {
                        if ($isModifyingSelf) {
                            $fail('You cannot modify your own role.');
                        }
                    }
                ],
                'status' => [
                    'sometimes',
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_SUSPENDED]),
                    // Prevent self status modification
                    function ($attribute, $value, $fail) use ($isModifyingSelf) {
                        if ($isModifyingSelf) {
                            $fail('You cannot modify your own status.');
                        }
                    }
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage (soft delete).
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = $request->user();

            // Prevent self-deletion
            if ($currentUser->id == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account through this endpoint.'
                ], 403);
            }

            // Prevent deletion of the last admin
            if ($user->isAdmin() && User::admins()->count() <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the last admin user.'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Toggle user status (activate/deactivate).
     */
    public function toggleStatus(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = $request->user();

            // Prevent self status modification
            if ($currentUser->id == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot modify your own status.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_SUSPENDED])]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status value.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => "User status updated to {$request->status}.",
                'data' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Change user password (admin only).
     */
    public function changePassword(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:8|confirmed',
                'force_change' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Optionally revoke all tokens to force re-login
            if ($request->get('force_change', false)) {
                $user->tokens()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully.',
                'meta' => [
                    'tokens_revoked' => $request->get('force_change', false)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Restore a soft deleted user.
     */
    public function restore(string $id): JsonResponse
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            if (!$user->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not deleted.'
                ], 400);
            }

            $user->restore();

            return response()->json([
                'success' => true,
                'message' => 'User restored successfully.',
                'data' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore user.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $currentUser = $request->user();

            // Prevent self-deletion
            if ($currentUser->id == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot permanently delete your own account.'
                ], 403);
            }

            $user->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete user.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Import users from array data
     */
    public function import(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'users' => 'required|array',
                'users.*.name' => 'required|string|max:255',
                'users.*.email' => 'required|email|max:255',
                'users.*.username' => 'nullable|string|max:255',
                'users.*.role' => 'nullable|string|in:admin,user',
                'users.*.status' => 'nullable|string|in:active,inactive',
                'users.*.password' => 'nullable|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $users = $request->input('users');
            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($users as $index => $userData) {
                try {
                    // Check if user already exists
                    $existingUser = User::where('email', $userData['email'])
                        ->orWhere(function($query) use ($userData) {
                            if (!empty($userData['username'])) {
                                $query->where('username', $userData['username']);
                            }
                        })
                        ->first();

                    if ($existingUser) {
                        $skipped++;
                        $errors[] = "Row {$index}: User with email '{$userData['email']}' already exists";
                        continue;
                    }

                    // Create new user
                    $user = User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'username' => $userData['username'] ?? null,
                        'role' => $userData['role'] ?? 'user',
                        'status' => $userData['status'] ?? 'active',
                        'password' => Hash::make($userData['password'] ?? 'password123'),
                        'email_verified_at' => now(),
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: " . $e->getMessage();
                    $skipped++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Import completed. Imported: {$imported}, Skipped: {$skipped}",
                'data' => [
                    'imported' => $imported,
                    'skipped' => $skipped,
                    'total' => count($users),
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}