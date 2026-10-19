<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Almanaque de Puno: fiestas patronales, rituales andinos y fechas cívicas
 * que se repiten cada año (mes y día), con su lugar y duración.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almanac_entries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            // fiesta | ritual | agricola | civica
            $table->string('kind', 12)->default('fiesta');
            $table->string('place_name')->nullable();
            $table->string('province')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedTinyInteger('month');
            $table->unsignedTinyInteger('day');
            $table->unsignedSmallInteger('duration_days')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['month', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almanac_entries');
    }
};
