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
        Schema::table('post_pg_guest_houses', function (Blueprint $table) {
            $table->string('car_parking', 2)->after('carpet_area')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_pg_guest_houses', function (Blueprint $table) {
            $table->dropColumn('car_parking');
        });
    }
};
