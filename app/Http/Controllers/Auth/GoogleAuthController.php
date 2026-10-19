<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // User exists, log them in with session
                Auth::login($user);
                $request->session()->regenerate();

                // Redirect based on role
                if ($user->role === 'admin' || $user->role === 'socio') {
                    return redirect()->away($this->frontendUrl('/dashboard'));
                }

                // Turistas van a home (el header se actualiza automáticamente)
                return redirect()->away($this->frontendUrl('/'));
            }

            // Check if user exists with this email (link Google account)
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar ?? $existingUser->avatar,
                ]);

                Auth::login($existingUser);
                $request->session()->regenerate();

                // Redirect based on role
                if ($existingUser->role === 'admin' || $existingUser->role === 'socio') {
                    return redirect()->away($this->frontendUrl('/dashboard'));
                }

                // Turistas van a home
                return redirect()->away($this->frontendUrl('/'));
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

            Auth::login($newUser);
            $request->session()->regenerate();

            // Redirect new tourist to home
            return redirect()->away($this->frontendUrl('/'));
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Google OAuth Error: ' . $e->getMessage());

            return redirect()->away($this->frontendUrl('/login?error=google'));
        }
    }

    /**
     * Build an absolute URL on the React frontend
     */
    private function frontendUrl(string $path): string
    {
        return rtrim(config('app.frontend_url'), '/') . $path;
    }
}
