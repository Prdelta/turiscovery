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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship (can favorite locales, eventos, experiencias, etc.)
            $table->morphs('favoritable');

            $table->timestamps();

            // Unique constraint to prevent duplicate favorites
            $table->unique(['user_id', 'favoritable_type', 'favoritable_id']);

            // Indexes
            $table->index('user_id');
            // $table->index(['favoritable_type', 'favoritable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
