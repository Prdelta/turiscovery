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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('locale_id')->nullable()->constrained()->onDelete('set null');

            // Content
            $table->string('title');
            $table->text('description');
            $table->longText('content')->nullable();

            // Time-Sensitive: Critical for filtering
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            // Geolocation
            $table->geography('location', 'point', 4326)->nullable();
            $table->string('address')->nullable();

            // Event Details
            $table->decimal('ticket_price', 10, 2)->nullable();
            $table->enum('category', [
                'concert',
                'festival',
                'nightlife',
                'cultural',
                'sports',
                'exhibition',
                'other'
            ])->default('other');

            // Media
            $table->json('images')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes (end_time is critical for time-sensitive filtering)
            $table->index('user_id');
            $table->index('locale_id');
            $table->index('category');
            $table->index('is_active');
            $table->index('start_time');
            $table->index('end_time');
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
