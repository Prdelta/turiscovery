<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSocioOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $role = $request->user()->role;

        if ($role !== 'socio' && $role !== 'admin') {
            abort(403, 'Acceso denegado. Solo socios y administradores pueden acceder a esta área.');
        }

        return $next($request);
    }
}
