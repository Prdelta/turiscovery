<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /** Intereses que el viajero puede marcar (tipos de contenido) */
    public const INTERESTS = ['experiencias', 'eventos', 'promociones', 'locales', 'candelaria'];

    /** Ritmo de viaje: se usa para sugerir experiencias según la dificultad */
    public const PACES = ['suave', 'moderado', 'intenso'];

    /**
     * Perfil del usuario autenticado con resumen de actividad
     */
    public function show(Request $request)
    {
        $user = $request->user()->loadCount(['favorites', 'reviews', 'bookings', 'eventAttendances', 'coupons']);

        return response()->json([
            'success' => true,
            'data' => $this->present($user),
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
            'preferences' => 'nullable|array:notifications,interests,pace',
            'preferences.notifications' => 'sometimes|array:bookings,promotions',
            'preferences.notifications.*' => 'boolean',
            'preferences.interests' => 'sometimes|array',
            'preferences.interests.*' => ['string', Rule::in(self::INTERESTS)],
            'preferences.pace' => ['sometimes', 'nullable', Rule::in(self::PACES)],
            'current_password' => 'required_with:password|nullable|string',
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if (! empty($validated['password'])) {
            if (! Hash::check($validated['current_password'] ?? '', $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual no es correcta.',
                    'errors' => ['current_password' => ['La contraseña actual no es correcta.']],
                ], 422);
            }

            $user->password = $validated['password']; // Hasheado por el cast
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->bio = $validated['bio'] ?? null;

        if (array_key_exists('preferences', $validated)) {
            // Se combinan con las guardadas: el formulario puede enviar solo una parte
            $prefs = array_replace($user->resolvedPreferences(), $validated['preferences'] ?? []);
            $prefs['interests'] = array_values(array_unique($prefs['interests'] ?? []));
            $user->preferences = $prefs;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'data' => $this->present($user),
        ]);
    }

    /**
     * Subir o quitar la foto de perfil
     */
    public function avatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required_without:remove|image|mimes:jpeg,png,webp|max:3072',
            'remove' => 'sometimes|boolean',
        ]);

        $this->deleteStoredAvatar($user->avatar);

        if ($request->boolean('remove')) {
            $user->avatar = null;
        } else {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $request->getSchemeAndHttpHost().'/storage/'.$path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->avatar ? 'Foto actualizada' : 'Foto eliminada',
            'data' => $this->present($user),
        ]);
    }

    /** Borra del disco la foto anterior si la subimos nosotros (no las de Google) */
    private function deleteStoredAvatar(?string $url): void
    {
        if ($url && str_contains($url, '/storage/avatars/')) {
            Storage::disk('public')->delete('avatars/'.basename(parse_url($url, PHP_URL_PATH)));
        }
    }

    private function present($user): array
    {
        return array_merge($user->toArray(), [
            'preferences' => $user->resolvedPreferences(),
            'email_verified_at' => $user->email_verified_at,
        ]);
    }
}
