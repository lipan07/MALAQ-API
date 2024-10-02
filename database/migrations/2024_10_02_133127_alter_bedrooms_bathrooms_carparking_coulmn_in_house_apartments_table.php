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
            $table->string('bedrooms', 2)->nullable()->change();
            $table->string('bathrooms', 2)->nullable()->change();
            $table->string('car_parking', 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_houses_apartments', function (Blueprint $table) {
            $table->integer('bedrooms')->change();
            $table->integer('bathrooms')->change();
            $table->integer('car_parking')->change();
        });
    }
};
