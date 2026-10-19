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
use App\Support\MorphTypes;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
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

        // Al cambiar contenido público, invalidar el caché de la API para que se vea al instante
        $models = [Locale::class, Evento::class, Experiencia::class, Promocion::class, Candelaria::class, CandelariaDanza::class, CandelariaGallery::class];
        foreach ($models as $model) {
            $model::saved(fn () => ApiQueryCacheMiddleware::clearCache());
            $model::deleted(fn () => ApiQueryCacheMiddleware::clearCache());
        }
    }
}
