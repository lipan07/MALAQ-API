<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('street_address', 255)->nullable()->after('screenshot_path');
            $table->string('city', 100)->nullable()->after('street_address');
            $table->string('pin_code', 10)->nullable()->after('city');
            $table->string('country', 100)->nullable()->after('pin_code');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['street_address', 'city', 'pin_code', 'country']);
        });
    }
};
