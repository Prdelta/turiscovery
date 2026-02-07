<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para cachear respuestas de API
 *
 * Cachea automáticamente respuestas GET exitosas para reducir
 * la carga en la base de datos y mejorar el tiempo de respuesta.
 */
class ApiQueryCacheMiddleware
{
    /**
     * Rutas que deben ser cacheadas (GET públicas)
     */
    protected array $cacheableRoutes = [
        'api/eventos',
        'api/experiencias',
        'api/promociones',
        'api/locales',
        'api/candelaria',
        'api/candelaria-danzas',
        'api/candelaria-gallery',
    ];

    /**
     * Tiempo de vida del caché en segundos
     */
    protected int $cacheTTL = 300; // 5 minutos

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo cachear requests GET
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Solo cachear rutas específicas
        if (!$this->shouldCache($request)) {
            return $next($request);
        }

        // Generar clave de caché única basada en la URL completa con parámetros
        $cacheKey = $this->getCacheKey($request);

        // Intentar obtener respuesta del caché
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse !== null) {
            // Retornar respuesta cacheada con header para debugging
            return response()->json($cachedResponse)
                ->header('X-Cache', 'HIT')
                ->header('X-Cache-Key', $cacheKey);
        }

        // Ejecutar request y obtener respuesta
        $response = $next($request);

        // Solo cachear respuestas exitosas (200-299)
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $content = json_decode($response->getContent(), true);

            // Solo cachear si la respuesta tiene datos
            if ($content && isset($content['success']) && $content['success'] === true) {
                // Cachear con TTL configurado
                Cache::put($cacheKey, $content, $this->cacheTTL);

                // Agregar header para debugging
                $response->header('X-Cache', 'MISS');
                $response->header('X-Cache-Key', $cacheKey);
            }
        }

        return $response;
    }

    /**
     * Determinar si la request debe ser cacheada
     */
    protected function shouldCache(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->cacheableRoutes as $route) {
            // Verificar si la ruta coincide (incluyendo con ID)
            if (str_starts_with($path, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generar clave de caché única
     */
    protected function getCacheKey(Request $request): string
    {
        // Incluir path, query params y usuario autenticado (si aplica)
        $parts = [
            'api-cache',
            $request->path(),
            http_build_query($request->query()),
            $request->user() ? $request->user()->id : 'guest',
        ];

        return 'api:' . md5(implode('|', $parts));
    }

    /**
     * Limpiar caché de una ruta específica
     * (Útil para cuando se crean/actualizan/eliminan recursos)
     */
    public static function clearCache(string $route): void
    {
        // Limpiar todo el caché de esa ruta
        Cache::forget("api-cache:{$route}");

        // Limpiar tags si se usan
        if (config('cache.default') === 'redis') {
            Cache::tags(['api-cache', $route])->flush();
        }
    }
}
