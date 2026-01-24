<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->enum('status', ['confirmed', 'maybe', 'cancelled'])->default('confirmed');
            $table->integer('guests')->default(1); // Número de acompañantes
            $table->timestamps();

            // Un usuario solo puede confirmar asistencia una vez por evento
            $table->unique(['user_id', 'evento_id']);
            $table->index(['evento_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendees');
    }
};
