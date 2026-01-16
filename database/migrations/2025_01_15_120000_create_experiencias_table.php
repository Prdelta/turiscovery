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
        Schema::create('experiencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('locale_id')->nullable()->constrained()->onDelete('set null');

            // Content
            $table->string('title');
            $table->text('description');
            $table->longText('content')->nullable();

            // Geolocation (optional - for experiences at specific locations)
            $table->geography('location', 'point', 4326)->nullable();
            $table->string('address')->nullable();

            // Experience Details
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('duration_hours')->nullable();
            $table->decimal('price_pen', 10, 2)->nullable(); // Price in Peruvian Soles
            $table->integer('max_participants')->nullable();

            // Media & Tags
            $table->json('images')->nullable();
            $table->json('tags')->nullable(); // e.g., ["adventure", "cultural", "nature"]

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('locale_id');
            $table->index('difficulty');
            $table->index('is_active');
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiencias');
    }
};
