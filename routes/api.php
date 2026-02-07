<?php

use App\Http\Controllers\Auth\{GoogleAuthController, LoginController, RegisterController, SessionController};
use App\Http\Controllers\Api\{
    BookingController,
    CandelariaController,
    CandelariaDanzaController,
    CandelariaGalleryController,
    EventAttendeeController,
    EventoController,
    ExperienciaController,
    FavoriteController,
    LocaleController,
    PromocionController,
    ReviewController,
    UserCouponController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========== Public Routes (No Authentication Required) ==========

// Authentication Routes (Legacy - Token Based)
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Session-Based Authentication (Secure - httpOnly Cookies)
Route::post('/session/login', [SessionController::class, 'login']);
Route::get('/session/check', [SessionController::class, 'check']);

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Public Read-Only Routes for 4 Pillars (with parameter validation + API cache)
Route::middleware(['validate.params', 'api.cache'])->group(function () {
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

    // Candelaria Gallery (Public)
    Route::get('/candelaria-gallery', [CandelariaGalleryController::class, 'index']);

    // Candelaria Danzas (Public)
    Route::get('/candelaria-danzas', [CandelariaDanzaController::class, 'index']);
    Route::get('/candelaria-danzas/featured', [CandelariaDanzaController::class, 'featured']);
    Route::get('/candelaria-danzas/{id}', [CandelariaDanzaController::class, 'show']);
});

// ========== Protected Routes (Sanctum Authentication Required) ==========

Route::middleware(['auth:sanctum', 'validate.params'])->group(function () {

    // User Info & Logout (Legacy - Token Based)
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/me', [LoginController::class, 'me']);

    // Session-Based Auth Routes
    Route::post('/session/logout', [SessionController::class, 'logout']);
    Route::get('/session/me', [SessionController::class, 'me']);

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

    // Bookings (Reservas de Experiencias)
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/check-availability', [BookingController::class, 'checkAvailability']);

    // Event Attendances (Confirmación de asistencia a eventos)
    Route::get('/event-attendances', [EventAttendeeController::class, 'index']);
    Route::post('/event-attendances', [EventAttendeeController::class, 'store']);
    Route::put('/event-attendances/{id}', [EventAttendeeController::class, 'update']);
    Route::delete('/event-attendances/{eventoId}', [EventAttendeeController::class, 'destroy']);
    Route::get('/event-attendances/check/{eventoId}', [EventAttendeeController::class, 'check']);
    Route::get('/event-attendances/count/{eventoId}', [EventAttendeeController::class, 'count']);

    // User Coupons (Cupones de promociones)
    Route::get('/user-coupons', [UserCouponController::class, 'index']);
    Route::post('/user-coupons', [UserCouponController::class, 'store']);
    Route::get('/user-coupons/{id}', [UserCouponController::class, 'show']);
    Route::post('/user-coupons/{id}/redeem', [UserCouponController::class, 'redeem']);

    // ========== Admin Routes (Full Control) ==========

    Route::middleware('role:admin')->group(function () {
        // Candelaria Gallery Management
        Route::post('/candelaria-gallery', [CandelariaGalleryController::class, 'store']);
        Route::put('/candelaria-gallery/{id}', [CandelariaGalleryController::class, 'update']);
        Route::delete('/candelaria-gallery/{id}', [CandelariaGalleryController::class, 'destroy']);

        // Candelaria Danzas Management
        Route::post('/candelaria-danzas', [CandelariaDanzaController::class, 'store']);
        Route::put('/candelaria-danzas/{id}', [CandelariaDanzaController::class, 'update']);
        Route::delete('/candelaria-danzas/{id}', [CandelariaDanzaController::class, 'destroy']);
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
