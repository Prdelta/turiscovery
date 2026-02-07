<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OptimizeDatabaseIndexes extends Command
{
    protected $signature = 'db:optimize-indexes
                            {--force : Aplicar índices sin confirmación}';

    protected $description = 'Agrega índices optimizados a las tablas principales';

    public function handle()
    {
        $this->info('🔧 Optimizando índices de base de datos...');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('¿Deseas agregar índices de optimización? (Puede tardar 1-3 minutos)', true)) {
                return self::SUCCESS;
            }
        }

        $tables = [
            'locales' => [
                ['is_active'],
                ['category'],
                ['user_id'],
                ['is_active', 'category', 'created_at'],
            ],
            'eventos' => [
                ['is_active'],
                ['start_time'],
                ['end_time'],
                ['category'],
                ['user_id'],
                ['is_active', 'end_time', 'start_time'],
            ],
            'experiencias' => [
                ['is_active'],
                ['difficulty'],
                ['user_id'],
                ['locale_id'],
                ['price_pen'],
                ['is_active', 'price_pen', 'created_at'],
            ],
            'promociones' => [
                ['is_active'],
                ['start_date'],
                ['end_date'],
                ['discount_type'],
                ['locale_id'],
                ['is_active', 'start_date', 'end_date', 'created_at'],
            ],
            'candelaria' => [
                ['is_active'],
                ['category'],
                ['is_featured'],
                ['is_active', 'is_featured', 'created_at'],
            ],
            'reviews' => [
                ['reviewable_type', 'reviewable_id'],
                ['user_id'],
                ['rating'],
            ],
            'favorites' => [
                ['favoriteable_type', 'favoriteable_id'],
                ['user_id'],
                ['user_id', 'favoriteable_type', 'favoriteable_id'],
            ],
            'bookings' => [
                ['user_id'],
                ['experiencia_id'],
                ['status'],
                ['date'],
                ['user_id', 'status', 'date'],
            ],
        ];

        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($tables as $table => $indexes) {
            // Verificar si la tabla existe
            if (!$this->tableExists($table)) {
                $this->warn("⚠️  Tabla '{$table}' no existe, saltando...");
                continue;
            }

            $this->line("📋 Procesando tabla: <fg=cyan>{$table}</>");

            foreach ($indexes as $columns) {
                $indexName = $this->generateIndexName($table, $columns);

                try {
                    // Intentar crear el índice
                    $this->createIndex($table, $columns, $indexName);
                    $this->line("  ✅ Índice creado: {$indexName}");
                    $created++;
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), 'already exists')) {
                        $this->line("  ⏭️  Índice ya existe: {$indexName}");
                        $skipped++;
                    } else {
                        $this->error("  ❌ Error en {$indexName}: " . $e->getMessage());
                        $errors++;
                    }
                }
            }

            $this->newLine();
        }

        // Resumen
        $this->info('========================================');
        $this->info('Resumen de Optimización:');
        $this->line("  ✅ Índices creados: <fg=green>{$created}</>");
        $this->line("  ⏭️  Índices existentes: <fg=yellow>{$skipped}</>");
        $this->line("  ❌ Errores: <fg=red>{$errors}</>");
        $this->info('========================================');

        if ($created > 0) {
            $this->newLine();
            $this->info('🎉 ¡Optimización completada! Las queries ahora serán más rápidas.');
        }

        return self::SUCCESS;
    }

    protected function tableExists(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function createIndex(string $table, array $columns, string $indexName): void
    {
        $columnsList = implode(', ', array_map(function ($col) {
            return '"' . $col . '"';
        }, $columns));

        $sql = "CREATE INDEX \"{$indexName}\" ON \"{$table}\" ({$columnsList})";

        DB::statement($sql);
    }

    protected function generateIndexName(string $table, array $columns): string
    {
        $suffix = implode('_', $columns);
        return "{$table}_{$suffix}_index";
    }
}
