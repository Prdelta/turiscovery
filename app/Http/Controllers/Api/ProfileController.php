<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Perfil del usuario autenticado con resumen de actividad
     */
    public function show(Request $request)
    {
        $user = $request->user()->loadCount(['favorites', 'reviews', 'bookings', 'eventAttendances', 'coupons']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Actualizar datos del perfil
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'preferences' => 'nullable|array',
            'current_password' => 'required_with:password|nullable|string',
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'] ?? '', $user->password)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['current_password' => ['La contraseña actual no es correcta.']],
                ], 422);
            }

            $user->password = $validated['password']; // Hasheado por el cast
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->bio = $validated['bio'] ?? null;

        if ($request->has('preferences')) {
            $user->preferences = $validated['preferences'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'data' => $user,
        ]);
    }
}
