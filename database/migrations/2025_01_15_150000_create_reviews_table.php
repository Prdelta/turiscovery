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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship (can review locales, eventos, experiencias, etc.)
            $table->morphs('reviewable');

            // Review Content
            $table->integer('rating')->unsigned(); // 1-5 stars
            $table->string('title')->nullable();
            $table->text('comment')->nullable();

            // Verification (for verified purchasers/participants)
            $table->boolean('is_verified')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            // $table->index(['reviewable_type', 'reviewable_id']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
