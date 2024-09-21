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
        Schema::table('post_shop_offices', function (Blueprint $table) {
            $table->string('construction_status', 20)->nullable()->after('listed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_shop_offices', function (Blueprint $table) {
            $table->dropColumn('construction_status');
        });
    }
};
