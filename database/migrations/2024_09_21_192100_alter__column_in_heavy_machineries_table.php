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
        Schema::table('post_heavy_machineries', function (Blueprint $table) {
            $table->dropColumn('hours_used');
            $table->dropColumn('price');
            // $table->dropColumn('model');
            $table->decimal('amount', 10, 2);
            $table->string('owner');
            $table->string('fuel_type');
            $table->string('listed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_heavy_machineries', function (Blueprint $table) {
            $table->string('hours_used');
            // $table->string('model');
            $table->decimal('price', 10, 2);
            $table->dropColumn('amount');
            $table->dropColumn('owner');
            $table->dropColumn('fuel_type');
            $table->dropColumn('listed_by');
        });
    }
};
