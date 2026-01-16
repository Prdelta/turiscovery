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
        Schema::create('candelaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('locale_id')->nullable()->constrained()->onDelete('set null');

            // Content
            $table->string('title');
            $table->text('description');
            $table->longText('content')->nullable();

            // Festival-specific
            $table->dateTime('event_date')->nullable();
            $table->enum('category', [
                'dance',
                'history',
                'costume',
                'music',
                'tradition',
                'procession',
                'other'
            ])->default('other');

            // Media
            $table->json('images')->nullable();

            // Status
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('locale_id');
            $table->index('category');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('event_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candelaria');
    }
};
