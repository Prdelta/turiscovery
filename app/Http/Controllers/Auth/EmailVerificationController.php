<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

/**
 * Verificación del correo. No bloquea el uso de la cuenta: solo confirma que el
 * correo es real para que los socios puedan contactar al viajero.
 */
class EmailVerificationController extends Controller
{
    /**
     * Enlace firmado del correo. No exige sesión: se puede abrir desde otro dispositivo.
     */
    public function verify(Request $request, int $id, string $hash)
    {
        $user = User::find($id);

        if (!$request->hasValidSignature()) {
            return redirect()->away($this->frontendUrl('/cuenta?verificado=vencido'));
        }

        if (!$user || !hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect()->away($this->frontendUrl('/cuenta?verificado=error'));
        }

        if (!$user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->away($this->frontendUrl('/cuenta?verificado=1'));
    }

    /**
     * Reenviar el correo de verificación al usuario con sesión
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Tu correo ya está verificado.',
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => "Te enviamos un nuevo enlace a {$user->email}.",
        ]);
    }

    private function frontendUrl(string $path): string
    {
        return rtrim(config('app.frontend_url'), '/') . $path;
    }
}
