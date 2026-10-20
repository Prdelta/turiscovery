<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * Recuperación de contraseña: envía el enlace por correo y fija la nueva clave.
 * El enlace apunta al frontend (/restablecer), ver AppServiceProvider.
 */
class PasswordResetController extends Controller
{
    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        // Se responde igual exista o no la cuenta, para no revelar qué correos están registrados.
        // Solo se avisa si se pidió otro enlace hace muy poco.
        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'success' => false,
                'message' => 'Ya te enviamos un enlace hace poco. Espera un minuto antes de pedir otro.',
            ], 429);
        }

        return response()->json([
            'success' => true,
            'message' => 'Si el correo está registrado, te enviamos un enlace para crear una nueva contraseña.',
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password, // Hasheado por el cast
                    'remember_token' => Str::random(60),
                    // Quien abrió el enlace demostró que el correo es suyo
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'message' => 'El enlace no es válido o ya venció. Pide uno nuevo.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Listo. Ya puedes iniciar sesión con tu nueva contraseña.',
        ]);
    }
}
