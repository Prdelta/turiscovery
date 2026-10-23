<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modo Candelaria en vivo: programa oficial de la festividad y orden de
 * presentación de los conjuntos en cada jornada (entrada, parada, concurso).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candelaria_program_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            // misa | procesion | concurso | entrada | parada | otro
            $table->string('type', 20)->default('otro');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('location_name')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            // Recorrido del desfile: lista de puntos [[lat, lng], ...]
            $table->json('route')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('candelaria_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_item_id')->constrained('candelaria_program_items')->cascadeOnDelete();
            $table->foreignId('candelaria_danza_id')->nullable()->constrained('candelaria_danzas')->nullOnDelete();
            $table->string('group_name');
            $table->unsignedInteger('order_number');
            // pending | performing | done | skipped
            $table->string('status', 12)->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['program_item_id', 'order_number']);
            $table->index(['program_item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candelaria_performances');
        Schema::dropIfExists('candelaria_program_items');
    }
};
