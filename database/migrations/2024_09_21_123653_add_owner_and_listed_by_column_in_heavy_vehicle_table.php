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
            $table->string('owner', 5)->nullable()->after('price');
            $table->string('listed_by', 10)->nullable()->after('owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_heavy_vehicles', function (Blueprint $table) {
            $table->dropColumn('owner');
            $table->dropColumn('listed_by');
        });
    }
};
