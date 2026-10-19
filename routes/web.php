<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| La interfaz es la SPA de React (carpeta frontend/). Laravel solo expone la
| API (/api) y el flujo OAuth de Google, que necesita sesión web.
|
*/

// Google OAuth: se usa el prefijo /api para coincidir con la URI registrada
// en Google Console, pero se define aquí para tener soporte de sesión.
Route::get('/api/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/api/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Laravel redirige aquí a los usuarios no autenticados en rutas web
Route::get('/login', fn () => redirect()->away(rtrim(config('app.frontend_url'), '/') . '/login'))->name('login');

// Cualquier otra URL web se envía a la misma ruta del frontend React
Route::fallback(function () {
    $path = request()->getRequestUri();

    if (str_starts_with($path, '/api/')) {
        abort(404);
    }

    return redirect()->away(rtrim(config('app.frontend_url'), '/') . $path);
});
