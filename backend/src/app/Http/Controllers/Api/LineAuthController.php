<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class LineAuthController extends Controller
{
    /**
     * Redirect to LINE login page
     */
    public function redirectToLine()
    {
        try {
            return Socialite::driver('line')->redirect();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to redirect to LINE login',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle LINE callback
     */
    public function handleLineCallback()
    {
        try {
            // Get user information from LINE
            $lineUser = Socialite::driver('line')->user();

            // Check if user already exists with this LINE ID
            $user = User::where('line_id', $lineUser->getId())->first();

            if ($user) {
                // Update existing user's LINE access token
                $user->update([
                    'line_access_token' => $lineUser->token,
                    'last_login_at' => now(),
                ]);
            } else {
                // Check if user exists with this email
                $existingUser = User::where('email', $lineUser->getEmail())->first();

                if ($existingUser) {
                    // Link LINE account to existing user
                    $existingUser->update([
                        'line_id' => $lineUser->getId(),
                        'line_access_token' => $lineUser->token,
                        'last_login_at' => now(),
                    ]);
                    $user = $existingUser;
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $lineUser->getName() ?? 'LINE User',
                        'email' => $lineUser->getEmail(),
                        'username' => $this->generateUsername($lineUser->getName() ?? 'lineuser'),
                        'line_id' => $lineUser->getId(),
                        'line_access_token' => $lineUser->token,
                        'avatar' => $lineUser->getAvatar(),
                        'role' => User::ROLE_USER,
                        'status' => User::STATUS_ACTIVE,
                        'email_verified_at' => now(), // LINE users are considered verified
                        'last_login_at' => now(),
                    ]);
                }
            }

            // Check if user account is active
            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is not active. Please contact administrator.',
                    'error' => 'Account ' . $user->status
                ], 403);
            }

            // Create token for API authentication
            $token = $user->createToken('line-auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'LINE login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'bio' => $user->bio,
                        'website' => $user->website,
                        'location' => $user->location,
                        'company' => $user->company,
                        'position' => $user->position,
                        'avatar' => $user->avatar,
                        'role' => $user->role,
                        'status' => $user->status,
                        'last_login_at' => $user->last_login_at,
                        'email_verified_at' => $user->email_verified_at,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ],
                    'token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'LINE login failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Generate unique username from name
     */
    private function generateUsername($name)
    {
        // Convert name to username format (lowercase, no spaces)
        $baseUsername = Str::slug($name, '');
        $username = $baseUsername;
        $counter = 1;

        // Check if username exists and generate unique one
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Unlink LINE account from user
     */
    public function unlinkLine(Request $request)
    {
        try {
            $user = $request->user();

            // Check if user has password (can still login without LINE)
            if (empty($user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot unlink LINE account. Please set a password first.'
                ], 400);
            }

            // Remove LINE connection
            $user->update([
                'line_id' => null,
                'line_access_token' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'LINE account unlinked successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlink LINE account',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
