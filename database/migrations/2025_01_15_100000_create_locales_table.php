<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Basic Information
            $table->string('name');
            $table->text('description');
            $table->string('address');

            // Contact Information
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Geolocation using PostGIS
            // Store as geography type for accurate distance calculations
            // Sin PostGIS (SQLite de los tests) se guarda como texto WKT; latitude/longitude son la fuente para leer
            DB::getDriverName() === 'pgsql'
                ? $table->geography('location', 'point', 4326)
                : $table->string('location')->nullable();

            // Category
            $table->enum('category', [
                'restaurant',
                'hotel',
                'tour_agency',
                'craft_shop',
                'museum',
                'cultural_center',
                'other',
            ])->default('other');

            // Media (JSON array of image paths)
            $table->json('images')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('category');
            $table->index('is_active');
            if (DB::getDriverName() === 'pgsql') {
                $table->spatialIndex('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
