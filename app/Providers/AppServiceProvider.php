<?php

namespace App\Providers;

use App\Http\Middleware\ApiQueryCacheMiddleware;
use App\Models\Candelaria;
use App\Models\CandelariaDanza;
use App\Models\CandelariaGallery;
use App\Models\Evento;
use App\Models\Experiencia;
use App\Models\Locale;
use App\Models\Promocion;
use App\Models\User;
use App\Support\MorphTypes;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Alias estables en las columnas *_type de reseñas, favoritos y tokens (ver MorphTypes)
        Relation::enforceMorphMap(MorphTypes::MAP);

        // Límite general de la API: por usuario autenticado o por IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Registro de cuentas: evita creación masiva
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(10)->by($request->ip());
        });

        // Correos de cuenta en español; el de contraseña lleva al frontend React
        $front = rtrim(config('app.frontend_url'), '/');

        ResetPassword::toMailUsing(fn (User $user, string $token) => (new MailMessage)
            ->subject('Crea una nueva contraseña')
            ->greeting("Hola, {$user->name}")
            ->line('Recibimos un pedido para cambiar la contraseña de tu cuenta de Turiscovery.')
            ->action('Crear nueva contraseña', "{$front}/restablecer?".http_build_query(['token' => $token, 'email' => $user->getEmailForPasswordReset()]))
            ->line('El enlace vence en '.config('auth.passwords.users.expire', 60).' minutos.')
            ->line('Si no lo pediste, ignora este correo: tu contraseña no cambia.'));

        VerifyEmail::toMailUsing(fn (User $user, string $url) => (new MailMessage)
            ->subject('Confirma tu correo')
            ->greeting("Hola, {$user->name}")
            ->line('Confirma que este correo es tuyo para que los anfitriones puedan escribirte sobre tus reservas.')
            ->action('Confirmar mi correo', $url)
            ->line('Si no creaste una cuenta en Turiscovery, ignora este correo.'));

        // Recuperar contraseña: pocos intentos por correo e IP
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });

        // Al cambiar contenido público, invalidar el caché de la API para que se vea al instante
        $models = [Locale::class, Evento::class, Experiencia::class, Promocion::class, Candelaria::class, CandelariaDanza::class, CandelariaGallery::class];
        foreach ($models as $model) {
            $model::saved(fn () => ApiQueryCacheMiddleware::clearCache());
            $model::deleted(fn () => ApiQueryCacheMiddleware::clearCache());
        }
    }
}
