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
        Schema::table('post_houses_apartments', function (Blueprint $table) {
            $table->string('bathrooms', 5)->nullable()->after('bedrooms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_houses_apartments', function (Blueprint $table) {
            $table->dropColumn('bathrooms');
        });
    }
};
