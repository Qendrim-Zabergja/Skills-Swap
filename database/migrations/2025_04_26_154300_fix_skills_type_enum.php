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
        Schema::table('skills', function (Blueprint $table) {
            // First drop the existing type column
            $table->dropColumn('type');
        });

        Schema::table('skills', function (Blueprint $table) {
            // Then recreate it with proper enum values
            $table->enum('type', ['teach', 'learn'])->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->enum('type', ['teaching', 'learning'])->after('name');
        });
    }
};
