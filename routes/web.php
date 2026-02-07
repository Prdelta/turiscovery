<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Los 4 Pilares
Route::get('/candelaria', function () {
    return view('candelaria.index');
});

Route::get('/experiencias', function () {
    return view('experiencias.index');
});

Route::get('/eventos', function () {
    return view('eventos.index');
});

Route::get('/promociones', function () {
    return view('promociones.index');
});

Route::get('/locales', function () {
    return view('locales.index');
});

// Detail Pages
Route::get('/candelaria/{id}', function () {
    return view('candelaria.show');
});

Route::get('/experiencias/{id}', function () {
    return view('experiencias.show');
});

Route::get('/eventos/{id}', function () {
    return view('eventos.show');
});

Route::get('/promociones/{id}', function () {
    return view('promociones.show');
});

// Search
Route::get('/search', function () {
    return view('search');
});

// Authentication
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'webLogin'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'webLogout'])->name('logout');

Route::get('/register', function () {
    return view('auth.register');
});

// Google OAuth Routes (Web Middleware for Session)
// We use /api prefix here to match the Google Console settings and Default config,
// but we define them in web.php to get Session support.
Route::get('/api/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/api/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback']);



// Dashboard (Protected - Solo Socios y Admins)
Route::middleware(['auth', 'socio.admin'])->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Resource Routes
    Route::resource('locales', \App\Http\Controllers\LocalController::class);
    Route::resource('eventos', \App\Http\Controllers\EventoController::class);
    Route::resource('promociones', \App\Http\Controllers\PromocionController::class);
    Route::resource('experiencias', \App\Http\Controllers\ExperienciaController::class);
});

// User Panel (Tourist)
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('user.index');
    Route::get('/favorites', [\App\Http\Controllers\UserController::class, 'favorites'])->name('user.favorites');
    Route::get('/reviews', [\App\Http\Controllers\UserController::class, 'reviews'])->name('user.reviews');
    Route::get('/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::put('/profile', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update');
});

// Admin Panel (Admin Only)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\AdminUserController::class);

    // Socios Management
    Route::get('socios/supervision', [\App\Http\Controllers\Admin\SocioController::class, 'supervision'])->name('admin.socios.supervision');
    Route::post('socios/{socio}/toggle-status', [\App\Http\Controllers\Admin\SocioController::class, 'toggleStatus'])->name('admin.socios.toggle-status');
    Route::resource('socios', \App\Http\Controllers\Admin\SocioController::class)->names([
        'index' => 'admin.socios.index',
        'create' => 'admin.socios.create',
        'store' => 'admin.socios.store',
        'show' => 'admin.socios.show',
        'edit' => 'admin.socios.edit',
        'update' => 'admin.socios.update',
        'destroy' => 'admin.socios.destroy',
    ]);

    // Candelaria Management
    Route::prefix('candelaria')->name('admin.candelaria.')->group(function () {
        // Gallery Management
        Route::resource('gallery', \App\Http\Controllers\Admin\CandelariaGalleryController::class)->except(['show']);

        // Danzas Management
        Route::resource('danzas', \App\Http\Controllers\Admin\CandelariaDanzaController::class)->except(['show']);

        // Resource Search
        Route::get('resources/search', [\App\Http\Controllers\Admin\ResourceSearchController::class, 'index'])->name('resources.search');
        Route::get('resources/search-images', [\App\Http\Controllers\Admin\ResourceSearchController::class, 'searchImages'])->name('resources.search-images');
        Route::get('resources/search-wikipedia', [\App\Http\Controllers\Admin\ResourceSearchController::class, 'searchWikipedia'])->name('resources.search-wikipedia');
    });
});

// TEST ROUTES - REMOVE IN PRODUCTION
require __DIR__ . '/test.php';
