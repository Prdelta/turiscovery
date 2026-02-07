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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role')->comment('Estado de la cuenta: activa o desactivada por admin');
            $table->timestamp('deactivated_at')->nullable()->after('is_active')->comment('Fecha en que se desactivó la cuenta');
            $table->unsignedBigInteger('deactivated_by')->nullable()->after('deactivated_at')->comment('ID del admin que desactivó la cuenta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'deactivated_at', 'deactivated_by']);
        });
    }
};
