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
        Schema::table('post_heavy_vehicles', function (Blueprint $table) {
            $table->dropColumn('model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_heavy_vehicles', function (Blueprint $table) {
            $table->string('model')->nullable();
        });
    }
};
