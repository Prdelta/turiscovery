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
});

Route::get('/register', function () {
    return view('auth.register');
});


// Dashboard (Protected)
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Resource Routes
    Route::resource('locales', \App\Http\Controllers\LocalController::class);
    Route::resource('eventos', \App\Http\Controllers\EventoController::class);
    Route::resource('promociones', \App\Http\Controllers\PromocionController::class);

    // Experiencias (Placeholder until controller is ready)
    Route::get('/experiencias', function () {
        return view('dashboard.experiencias.index');
    });
    Route::get('/experiencias/create', function () {
        return view('dashboard.experiencias.create');
    });
});

// User Panel (Tourist)
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('user.index');
    Route::get('/favorites', [\App\Http\Controllers\UserController::class, 'favorites'])->name('user.favorites');
    Route::get('/reviews', [\App\Http\Controllers\UserController::class, 'reviews'])->name('user.reviews');
    Route::get('/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::put('/profile', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update');
});

// TEST ROUTES - REMOVE IN PRODUCTION
require __DIR__ . '/test.php';
