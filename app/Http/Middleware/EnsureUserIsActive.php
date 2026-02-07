<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (auth()->check()) {
            $user = auth()->user();

            // Si el usuario no está activo, cerrar sesión y redirigir
            if (!$user->is_active) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('error', 'Tu cuenta ha sido desactivada por un administrador. Contacta con soporte para más información.');
            }
        }

        return $next($request);
    }
}
