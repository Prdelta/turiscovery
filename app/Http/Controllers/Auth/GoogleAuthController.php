<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth consent screen
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // User exists, log them in
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful with Google',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'avatar' => $user->avatar,
                        ],
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                    ]
                ], 200);
            }

            // Check if user exists with this email (link Google account)
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar ?? $existingUser->avatar,
                ]);

                $token = $existingUser->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Google account linked successfully',
                    'data' => [
                        'user' => [
                            'id' => $existingUser->id,
                            'name' => $existingUser->name,
                            'email' => $existingUser->email,
                            'role' => $existingUser->role,
                            'avatar' => $existingUser->avatar,
                        ],
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                    ]
                ], 200);
            }

            // Create new user with Google account
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => Hash::make(Str::random(32)), // Random password
                'role' => 'tourist', // Default role
                'email_verified_at' => now(), // Google email is verified
            ]);

            $token = $newUser->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully with Google',
                'data' => [
                    'user' => [
                        'id' => $newUser->id,
                        'name' => $newUser->name,
                        'email' => $newUser->email,
                        'role' => $newUser->role,
                        'avatar' => $newUser->avatar,
                    ],
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google authentication failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
