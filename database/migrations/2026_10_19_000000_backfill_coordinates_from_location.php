<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Copia latitud/longitud desde la columna PostGIS `location` a las columnas
 * numéricas en los registros antiguos, para no consultar PostGIS fila por fila
 * al serializar (evita N+1 en los listados).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach (['locales', 'eventos', 'experiencias'] as $table) {
            DB::statement("
                UPDATE {$table}
                SET latitude = ST_Y(location::geometry),
                    longitude = ST_X(location::geometry)
                WHERE location IS NOT NULL
                  AND (latitude IS NULL OR longitude IS NULL)
            ");
        }
    }

    public function down(): void
    {
        // Sin reversión: los datos copiados son equivalentes a `location`
    }
};
