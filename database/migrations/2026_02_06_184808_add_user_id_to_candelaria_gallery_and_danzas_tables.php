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
        // Add user_id to candelaria_gallery
        Schema::table('candelaria_gallery', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Admin que agregó la foto');
        });

        // Add user_id to candelaria_danzas
        Schema::table('candelaria_danzas', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Admin que agregó la danza');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candelaria_gallery', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('candelaria_danzas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
