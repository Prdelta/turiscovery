<?php

use App\Http\Controllers\Auth\{GoogleAuthController, LoginController, RegisterController};
use App\Http\Controllers\Api\{
    CandelariaController,
    EventoController,
    ExperienciaController,
    FavoriteController,
    LocaleController,
    PromocionController,
    ReviewController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========== Public Routes (No Authentication Required) ==========

// Authentication Routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Public Read-Only Routes for 4 Pillars
Route::get('/candelaria', [CandelariaController::class, 'index']);
Route::get('/candelaria/{id}', [CandelariaController::class, 'show']);

Route::get('/experiencias', [ExperienciaController::class, 'index']);
Route::get('/experiencias/{id}', [ExperienciaController::class, 'show']);

Route::get('/eventos', [EventoController::class, 'index']);
Route::get('/eventos/{id}', [EventoController::class, 'show']);

Route::get('/promociones', [PromocionController::class, 'index']);
Route::get('/promociones/{id}', [PromocionController::class, 'show']);

Route::get('/locales', [LocaleController::class, 'index']);
Route::get('/locales/{id}', [LocaleController::class, 'show']);

// ========== Protected Routes (Sanctum Authentication Required) ==========

Route::middleware('auth:sanctum')->group(function () {

    // User Info & Logout
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/me', [LoginController::class, 'me']);

    // ========== Socio Routes (Partners can manage their own content) ==========

    Route::middleware('role:socio,admin')->group(function () {

        // Locales (Partner Venues) - Full CRUD for own locales
        Route::post('/locales', [LocaleController::class, 'store']);
        Route::put('/locales/{id}', [LocaleController::class, 'update']);
        Route::delete('/locales/{id}', [LocaleController::class, 'destroy']);

        // Candelaria - Full CRUD for own content
        Route::post('/candelaria', [CandelariaController::class, 'store']);
        Route::put('/candelaria/{id}', [CandelariaController::class, 'update']);
        Route::delete('/candelaria/{id}', [CandelariaController::class, 'destroy']);

        // Experiencias - Full CRUD for own content
        Route::post('/experiencias', [ExperienciaController::class, 'store']);
        Route::put('/experiencias/{id}', [ExperienciaController::class, 'update']);
        Route::delete('/experiencias/{id}', [ExperienciaController::class, 'destroy']);

        // Eventos - Full CRUD for own content
        Route::post('/eventos', [EventoController::class, 'store']);
        Route::put('/eventos/{id}', [EventoController::class, 'update']);
        Route::delete('/eventos/{id}', [EventoController::class, 'destroy']);

        // Promociones - Full CRUD for own content
        Route::post('/promociones', [PromocionController::class, 'store']);
        Route::put('/promociones/{id}', [PromocionController::class, 'update']);
        Route::delete('/promociones/{id}', [PromocionController::class, 'destroy']);
    });

    // ========== Tourist Routes (Reviews, Favorites) ==========

    // All authenticated users can create reviews and favorites

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index']); // Get reviews for item
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::get('/my-reviews', [ReviewController::class, 'myReviews']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::get('/favorites/check', [FavoriteController::class, 'check']);
    Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);

    // ========== Admin Routes (Full Control) ==========

    Route::middleware('role:admin')->group(function () {
        // Admins have access to all routes above
        // Plus additional admin-only routes if needed

        // Example: Admin analytics, user management, etc.
        // Route::get('/admin/analytics', [AdminController::class, 'analytics']);
        // Route::get('/admin/users', [AdminController::class, 'users']);
    });
});

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Turiscovery API is working!',
        'timestamp' => now(),
    ]);
});
