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
        Schema::table('englo_contents', function (Blueprint $table) {
            $table->string('video_path', 500)->nullable()->after('language_id');
        });
        Schema::table('englo_contents', function (Blueprint $table) {
            $table->dropColumn(['url', 'duration_seconds']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('englo_contents', function (Blueprint $table) {
            $table->string('url')->nullable()->after('language_id');
            $table->unsignedSmallInteger('duration_seconds')->nullable()->after('url');
        });
        Schema::table('englo_contents', function (Blueprint $table) {
            $table->dropColumn('video_path');
        });
    }
};
