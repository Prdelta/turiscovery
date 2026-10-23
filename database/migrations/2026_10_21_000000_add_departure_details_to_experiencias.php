<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos de salida de cada experiencia que muestra la ficha y la reserva:
 * altura máxima, esfuerzo, punto de encuentro y hora de salida.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiencias', function (Blueprint $table) {
            $table->unsignedSmallInteger('altitude_m')->nullable()->after('max_participants'); // Altura máxima que se alcanza
            $table->string('effort_note', 255)->nullable()->after('altitude_m');             // "540 escalones desde el muelle"
            $table->string('meeting_point', 255)->nullable()->after('effort_note');          // "Muelle de Puno"
            $table->time('meeting_time')->nullable()->after('meeting_point');                // Hora de salida habitual
        });
    }

    public function down(): void
    {
        Schema::table('experiencias', function (Blueprint $table) {
            $table->dropColumn(['altitude_m', 'effort_note', 'meeting_point', 'meeting_time']);
        });
    }
};
