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
        // Agregar campos detallados a eventos
        Schema::table('eventos', function (Blueprint $table) {
            $table->string('street')->nullable()->after('address');
            $table->string('street_number')->nullable()->after('street');
            $table->string('district')->nullable()->after('street_number');
            $table->string('city')->default('Puno')->after('district');
            $table->string('region')->default('Puno')->after('city');
            $table->string('country')->default('Perú')->after('region');
            $table->string('postal_code')->nullable()->after('country');
            $table->text('address_notes')->nullable()->after('postal_code');
            $table->decimal('latitude', 10, 8)->nullable()->after('address_notes');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        // Agregar campos detallados a experiencias
        Schema::table('experiencias', function (Blueprint $table) {
            $table->string('street')->nullable()->after('address');
            $table->string('street_number')->nullable()->after('street');
            $table->string('district')->nullable()->after('street_number');
            $table->string('city')->default('Puno')->after('district');
            $table->string('region')->default('Puno')->after('city');
            $table->string('country')->default('Perú')->after('region');
            $table->string('postal_code')->nullable()->after('country');
            $table->text('address_notes')->nullable()->after('postal_code');
            $table->decimal('latitude', 10, 8)->nullable()->after('address_notes');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn([
                'street', 'street_number', 'district', 'city', 'region',
                'country', 'postal_code', 'address_notes', 'latitude', 'longitude'
            ]);
        });

        Schema::table('experiencias', function (Blueprint $table) {
            $table->dropColumn([
                'street', 'street_number', 'district', 'city', 'region',
                'country', 'postal_code', 'address_notes', 'latitude', 'longitude'
            ]);
        });
    }
};
