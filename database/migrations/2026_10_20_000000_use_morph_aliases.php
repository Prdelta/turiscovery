<?php

use App\Support\MorphTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cambia los nombres de clase guardados en las relaciones polimórficas
 * (App\Models\Experiencia) por alias estables (experiencia). Ver App\Support\MorphTypes.
 */
return new class extends Migration
{
    /** Tabla => columna *_type */
    private array $columns = [
        'reviews' => 'reviewable_type',
        'favorites' => 'favoritable_type',
        'personal_access_tokens' => 'tokenable_type',
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $column) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach (MorphTypes::MAP as $alias => $class) {
                DB::table($table)->where($column, $class)->update([$column => $alias]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->columns as $table => $column) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach (MorphTypes::MAP as $alias => $class) {
                DB::table($table)->where($column, $alias)->update([$column => $class]);
            }
        }
    }
};
