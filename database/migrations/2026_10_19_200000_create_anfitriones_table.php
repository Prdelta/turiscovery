<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Anfitriones: las personas que reciben al viajero (familias uros, tejedores de Taquile,
 * guías). Cada uno tiene su historia y un saludo grabado en su idioma.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anfitriones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('community')->nullable(); // Comunidad o isla: Uros Titino, Taquile...
            $table->string('role_title')->nullable(); // Tejedor, guía, pescador...
            $table->json('languages')->nullable(); // quechua | aimara | espanol | ingles
            $table->string('photo_url')->nullable();
            $table->text('story')->nullable();
            $table->text('greeting')->nullable(); // Saludo en su idioma
            $table->text('greeting_translation')->nullable();
            $table->string('audio_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });

        Schema::table('experiencias', function (Blueprint $table) {
            $table->foreignId('anfitrion_id')->nullable()->after('locale_id')->constrained('anfitriones')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('experiencias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anfitrion_id');
        });
        Schema::dropIfExists('anfitriones');
    }
};
