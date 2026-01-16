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
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('locale_id')->constrained()->onDelete('cascade');

            // Content
            $table->string('title');
            $table->text('description');

            // Discount Information
            $table->enum('discount_type', ['2x1', 'percentage', 'fixed'])->default('percentage');
            $table->integer('discount_percentage')->nullable(); // e.g., 20 for 20% off
            $table->decimal('discount_amount', 10, 2)->nullable(); // Fixed amount off
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();

            // Time-Sensitive: Critical for filtering
            $table->dateTime('start_date');
            $table->dateTime('end_date');

            // Additional Info
            $table->text('terms_conditions')->nullable();
            $table->string('redemption_code')->nullable();

            // Media
            $table->json('images')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes (end_date is critical for time-sensitive filtering)
            $table->index('user_id');
            $table->index('locale_id');
            $table->index('discount_type');
            $table->index('is_active');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};
