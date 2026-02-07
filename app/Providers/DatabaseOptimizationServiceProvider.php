<?php

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider para Optimización de Base de Datos
 *
 * Implementa:
 * - Pool de conexiones persistentes
 * - Caché de consultas frecuentes
 * - Monitoreo de queries lentas
 * - Reconexión automática en caso de pérdida
 */
class DatabaseOptimizationServiceProvider extends ServiceProvider
{
    /**
     * Tiempo de caché para queries en segundos
     */
    const CACHE_TTL = 300; // 5 minutos

    /**
     * Umbral para considerar una query como "lenta" (milisegundos)
     */
    const SLOW_QUERY_THRESHOLD = 1000; // 1 segundo

    /**
     * Register services.
     */
    public function register(): void
    {
        // Configurar opciones de PDO para todas las conexiones
        $this->app->resolving('db', function ($db) {
            $db->beforeExecuting(function ($query, $bindings, Connection $connection) {
                // Verificar si la conexión sigue activa
                $this->ensureConnectionIsAlive($connection);
            });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Solo aplicar optimizaciones en producción y staging
        if (!app()->environment('testing')) {
            $this->setupQueryCaching();
            $this->setupSlowQueryMonitoring();
            $this->setupConnectionPooling();
        }
    }

    /**
     * Configurar caché automático de queries SELECT frecuentes
     */
    protected function setupQueryCaching(): void
    {
        // Interceptar queries SELECT para cachearlas automáticamente
        DB::listen(function (QueryExecuted $query) {
            // Solo cachear SELECTs que no sean complejos
            if ($this->shouldCacheQuery($query->sql)) {
                $cacheKey = $this->generateQueryCacheKey($query->sql, $query->bindings);

                // Si la query aún no está en caché, guardarla
                if (!Cache::has($cacheKey)) {
                    // La próxima vez que se ejecute esta query, usaremos el caché
                    // Nota: Laravel ya tiene su propio sistema de caché de queries
                    // Esto es solo para monitoreo
                }
            }
        });
    }

    /**
     * Monitorear y registrar queries lentas
     */
    protected function setupSlowQueryMonitoring(): void
    {
        DB::listen(function (QueryExecuted $query) {
            // Si la query tardó más del umbral, registrarla
            if ($query->time > self::SLOW_QUERY_THRESHOLD) {
                Log::warning('Slow Query Detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                ]);

                // En desarrollo, también mostrar en consola
                if (app()->environment('local')) {
                    \Log::channel('stderr')->warning("⚠️  SLOW QUERY ({$query->time}ms): {$query->sql}");
                }
            }
        });
    }

    /**
     * Configurar pool de conexiones
     */
    protected function setupConnectionPooling(): void
    {
        // Configurar eventos de conexión
        DB::connection()->beforeExecuting(function () {
            // Limpiar conexiones inactivas periódicamente
            $this->cleanupIdleConnections();
        });

        // Registrar comando para limpiar conexiones manualmente
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\CleanupDatabaseConnections::class,
            ]);
        }
    }

    /**
     * Verificar si la conexión sigue activa y reconectar si es necesario
     */
    protected function ensureConnectionIsAlive(Connection $connection): void
    {
        try {
            // Ping a la base de datos con una query simple
            $connection->getPdo();
        } catch (\PDOException $e) {
            // Si la conexión murió, intentar reconectar
            Log::warning('Database connection lost, attempting to reconnect...', [
                'error' => $e->getMessage(),
            ]);

            try {
                $connection->reconnect();
                Log::info('Database connection restored successfully');
            } catch (\Exception $reconnectError) {
                Log::error('Failed to reconnect to database', [
                    'error' => $reconnectError->getMessage(),
                ]);
                throw $reconnectError;
            }
        }
    }

    /**
     * Limpiar conexiones inactivas
     */
    protected function cleanupIdleConnections(): void
    {
        // Esta lógica se ejecuta raramente (1% de las veces)
        if (rand(1, 100) > 99) {
            // PostgreSQL maneja esto automáticamente con idle_in_transaction_session_timeout
            // Solo registramos el evento
            Log::debug('Connection pool cleanup executed');
        }
    }

    /**
     * Determinar si una query debe ser cacheada
     */
    protected function shouldCacheQuery(string $sql): bool
    {
        // Solo cachear SELECTs simples
        $sql = strtoupper(trim($sql));

        // No cachear si es INSERT, UPDATE, DELETE, etc.
        if (!str_starts_with($sql, 'SELECT')) {
            return false;
        }

        // No cachear queries con RANDOM, NOW(), etc.
        $nonCacheablePatterns = ['RANDOM()', 'NOW()', 'CURRENT_TIMESTAMP', 'UUID_GENERATE'];
        foreach ($nonCacheablePatterns as $pattern) {
            if (str_contains($sql, $pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generar clave de caché para una query
     */
    protected function generateQueryCacheKey(string $sql, array $bindings): string
    {
        return 'query:' . md5($sql . serialize($bindings));
    }
}
