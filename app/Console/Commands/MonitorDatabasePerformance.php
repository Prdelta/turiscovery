<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PDO;

class MonitorDatabasePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:monitor
                            {--connections : Mostrar conexiones activas}
                            {--cache : Mostrar estadísticas de caché}
                            {--queries : Mostrar queries recientes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitorea el rendimiento de la base de datos y el caché';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 Monitor de Rendimiento de Base de Datos - Turiscovery');
        $this->newLine();

        if ($this->option('connections')) {
            $this->showConnections();
        } elseif ($this->option('cache')) {
            $this->showCacheStats();
        } elseif ($this->option('queries')) {
            $this->showRecentQueries();
        } else {
            // Mostrar resumen general
            $this->showGeneralStats();
        }

        return self::SUCCESS;
    }

    /**
     * Mostrar estadísticas generales
     */
    protected function showGeneralStats(): void
    {
        $this->info('=== Resumen General ===');
        $this->newLine();

        // Conexión actual
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $database = $connection->getDatabaseName();

        $this->line("🔗 <fg=cyan>Conexión:</> {$driver}");
        $this->line("🗄️  <fg=cyan>Base de Datos:</> {$database}");

        // Configuración de pool
        $persistent = config('database.connections.pgsql.options.' . PDO::ATTR_PERSISTENT);
        $this->line("♻️  <fg=cyan>Conexiones Persistentes:</> " . ($persistent ? '<fg=green>✓ Habilitado</>' : '<fg=red>✗ Deshabilitado</>'));

        // Timeout configurado
        $timeout = config('database.connections.pgsql.options.' . PDO::ATTR_TIMEOUT, 'N/A');
        $this->line("⏱️  <fg=cyan>Timeout de Conexión:</> {$timeout}s");

        $this->newLine();

        // Estadísticas de PostgreSQL
        if ($driver === 'pgsql') {
            $this->showPostgresStats();
        }

        $this->newLine();
        $this->info('💡 Usa --connections, --cache o --queries para más detalles');
    }

    /**
     * Mostrar estadísticas de PostgreSQL
     */
    protected function showPostgresStats(): void
    {
        try {
            // Tamaño de la base de datos
            $size = DB::selectOne("
                SELECT pg_size_pretty(pg_database_size(?)) as size
            ", [DB::connection()->getDatabaseName()]);

            $this->line("💾 <fg=cyan>Tamaño de BD:</> {$size->size}");

            // Número de tablas
            $tables = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.tables
                WHERE table_schema = 'public'
                AND table_type = 'BASE TABLE'
            ");

            $this->line("📋 <fg=cyan>Tablas:</> {$tables[0]->count}");

            // Conexiones activas
            $connections = DB::select("
                SELECT COUNT(*) as count
                FROM pg_stat_activity
                WHERE datname = ?
            ", [DB::connection()->getDatabaseName()]);

            $this->line("👥 <fg=cyan>Conexiones Activas:</> {$connections[0]->count}");

        } catch (\Exception $e) {
            $this->error('Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar conexiones activas
     */
    protected function showConnections(): void
    {
        $this->info('=== Conexiones Activas (PostgreSQL) ===');
        $this->newLine();

        try {
            $connections = DB::select("
                SELECT
                    pid,
                    usename,
                    application_name,
                    client_addr,
                    state,
                    state_change,
                    query_start,
                    wait_event_type,
                    wait_event
                FROM pg_stat_activity
                WHERE datname = ?
                ORDER BY state_change DESC
                LIMIT 20
            ", [DB::connection()->getDatabaseName()]);

            if (empty($connections)) {
                $this->warn('No hay conexiones activas');
                return;
            }

            $tableData = array_map(function ($conn) {
                return [
                    $conn->pid,
                    $conn->usename,
                    $conn->application_name ?: 'N/A',
                    $conn->client_addr ?: 'local',
                    $conn->state,
                    $conn->wait_event ?: '-',
                ];
            }, $connections);

            $this->table(
                ['PID', 'Usuario', 'App', 'Cliente', 'Estado', 'Wait Event'],
                $tableData
            );

            // Resumen por estado
            $states = DB::select("
                SELECT state, COUNT(*) as count
                FROM pg_stat_activity
                WHERE datname = ?
                GROUP BY state
            ", [DB::connection()->getDatabaseName()]);

            $this->newLine();
            $this->info('Resumen por Estado:');
            foreach ($states as $state) {
                $this->line("  • {$state->state}: {$state->count}");
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar estadísticas de caché
     */
    protected function showCacheStats(): void
    {
        $this->info('=== Estadísticas de Caché ===');
        $this->newLine();

        $driver = config('cache.default');
        $this->line("🔧 <fg=cyan>Driver de Caché:</> {$driver}");

        // Intentar obtener estadísticas del caché
        try {
            // Contar claves de API cacheadas
            $apiCacheCount = 0;
            if ($driver === 'database') {
                $cacheKeys = DB::table('cache')->where('key', 'like', 'api:%')->count();
                $apiCacheCount = $cacheKeys;
            }

            $this->line("📦 <fg=cyan>Entries de API en Caché:</> {$apiCacheCount}");

            // Probar hit/miss ratio (simulado)
            $testKey = 'test_' . time();
            Cache::put($testKey, 'test', 1);
            $hit = Cache::has($testKey);
            Cache::forget($testKey);

            $this->line("✅ <fg=cyan>Test de Caché:</> " . ($hit ? '<fg=green>OK</>' : '<fg=red>ERROR</>'));

        } catch (\Exception $e) {
            $this->error('Error al obtener estadísticas de caché: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('💡 Usa php artisan cache:clear para limpiar el caché');
    }

    /**
     * Mostrar queries recientes
     */
    protected function showRecentQueries(): void
    {
        $this->info('=== Queries Recientes (PostgreSQL) ===');
        $this->newLine();

        try {
            $queries = DB::select("
                SELECT
                    query,
                    calls,
                    total_exec_time,
                    mean_exec_time,
                    max_exec_time
                FROM pg_stat_statements
                WHERE dbid = (SELECT oid FROM pg_database WHERE datname = ?)
                ORDER BY mean_exec_time DESC
                LIMIT 10
            ", [DB::connection()->getDatabaseName()]);

            if (empty($queries)) {
                $this->warn('pg_stat_statements no está habilitado o no hay datos');
                $this->info('Habilita pg_stat_statements en postgresql.conf para ver estadísticas de queries');
                return;
            }

            $tableData = array_map(function ($q) {
                return [
                    substr($q->query, 0, 60) . '...',
                    $q->calls,
                    round($q->mean_exec_time, 2) . 'ms',
                    round($q->max_exec_time, 2) . 'ms',
                ];
            }, $queries);

            $this->table(
                ['Query', 'Calls', 'Avg Time', 'Max Time'],
                $tableData
            );

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->warn('pg_stat_statements probablemente no está habilitado en PostgreSQL');
        }
    }
}
