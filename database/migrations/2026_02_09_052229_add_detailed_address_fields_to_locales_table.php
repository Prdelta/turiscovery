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
        Schema::table('locales', function (Blueprint $table) {
            // Campos de dirección estructurada
            $table->string('street')->nullable()->after('address'); // Calle
            $table->string('street_number')->nullable()->after('street'); // Número
            $table->string('district')->nullable()->after('street_number'); // Distrito/Barrio
            $table->string('city')->default('Puno')->after('district'); // Ciudad
            $table->string('region')->default('Puno')->after('city'); // Región/Departamento
            $table->string('country')->default('Perú')->after('region'); // País
            $table->string('postal_code')->nullable()->after('country'); // Código postal
            $table->text('address_notes')->nullable()->after('postal_code'); // Referencias (Ej: "Al frente del parque")

            // Coordenadas explícitas para facilitar el acceso
            $table->decimal('latitude', 10, 8)->nullable()->after('address_notes');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locales', function (Blueprint $table) {
            $table->dropColumn([
                'street',
                'street_number',
                'district',
                'city',
                'region',
                'country',
                'postal_code',
                'address_notes',
                'latitude',
                'longitude',
            ]);
        });
    }
};
