<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Controlador de sesiones con autenticación basada en cookies httpOnly
 *
 * Este controlador implementa autenticación segura usando cookies httpOnly de Sanctum
 * en lugar de tokens en localStorage, previniendo ataques XSS de robo de tokens.
 */
class SessionController extends Controller
{
    /**
     * Login con sesión (no token en localStorage)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Rate limiting por IP
        $key = 'login.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => "Demasiados intentos de inicio de sesión. Por favor, intenta de nuevo en {$seconds} segundos.",
            ], 429);
        }

        // Buscar usuario
        $user = User::where('email', $request->email)->first();

        // Verificar credenciales
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60); // Incrementar contador por 60 segundos

            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Limpiar rate limiter si el login es exitoso
        RateLimiter::clear($key);

        // Login vía sesión de Laravel (NO genera token)
        // La cookie de sesión se establece automáticamente y es httpOnly
        Auth::login($user, $request->boolean('remember'));

        // Regenerar ID de sesión para prevenir session fixation
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                ],
                // NO se devuelve token - la cookie de sesión se establece automáticamente
                // y es httpOnly (no accesible desde JavaScript)
            ]
        ], 200);
    }

    /**
     * Logout - Invalida la sesión
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Cerrar sesión de Laravel
        Auth::logout();

        // Invalidar sesión actual
        $request->session()->invalidate();

        // Regenerar token CSRF
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
        ], 200);
    }

    /**
     * Obtener información del usuario autenticado
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                    'created_at' => $user->created_at,
                ],
            ],
        ], 200);
    }

    /**
     * Verificar estado de autenticación
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        return response()->json([
            'success' => true,
            'authenticated' => Auth::check(),
            'user' => Auth::check() ? [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
            ] : null,
        ], 200);
    }
}
