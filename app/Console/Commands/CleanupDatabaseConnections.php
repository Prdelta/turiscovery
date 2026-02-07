<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDatabaseConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:cleanup-connections
                            {--force : Forzar cierre de todas las conexiones idle}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia conexiones inactivas de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Limpiando conexiones inactivas...');

        try {
            // Reconectar todas las conexiones
            DB::purge();

            $this->info('✅ Conexiones limpiadas exitosamente');

            // Si se usa --force, también limpiar conexiones en el servidor
            if ($this->option('force') && DB::getDriverName() === 'pgsql') {
                $this->cleanupPostgresConnections();
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error al limpiar conexiones: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Limpiar conexiones idle en PostgreSQL
     */
    protected function cleanupPostgresConnections(): void
    {
        $this->warn('⚠️  Forzando limpieza de conexiones idle en PostgreSQL...');

        try {
            // Obtener conexiones idle (más de 5 minutos)
            $idleConnections = DB::select("
                SELECT pid, usename, application_name, state, state_change
                FROM pg_stat_activity
                WHERE state = 'idle'
                AND state_change < NOW() - INTERVAL '5 minutes'
                AND pid != pg_backend_pid()
            ");

            if (count($idleConnections) === 0) {
                $this->info('No hay conexiones idle para limpiar');
                return;
            }

            $this->table(
                ['PID', 'Usuario', 'App', 'Estado', 'Desde'],
                array_map(function ($conn) {
                    return [
                        $conn->pid,
                        $conn->usename,
                        $conn->application_name,
                        $conn->state,
                        $conn->state_change,
                    ];
                }, $idleConnections)
            );

            if ($this->confirm('¿Deseas terminar estas conexiones?', true)) {
                $terminated = 0;
                foreach ($idleConnections as $conn) {
                    try {
                        DB::select("SELECT pg_terminate_backend(?)", [$conn->pid]);
                        $terminated++;
                    } catch (\Exception $e) {
                        // Ignorar errores (la conexión ya puede estar cerrada)
                    }
                }

                $this->info("✅ {$terminated} conexiones terminadas");
            }
        } catch (\Exception $e) {
            $this->error('Error al limpiar conexiones PostgreSQL: ' . $e->getMessage());
        }
    }
}
