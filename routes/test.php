<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/test-token-creation', function () {
    try {
        $user = User::where('email', 'socio@example.com')->first();

        if (!$user) {
            return response()->json(['error' => 'User not found']);
        }

        $token = $user->createToken('test_token');

        return response()->json([
            'success' => true,
            'message' => 'Token created successfully!',
            'token' => $token->plainTextToken
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
