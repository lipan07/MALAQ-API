<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds podcast type: podcast_genre_id when set indicates podcast content.
     * genre_id and language_id become nullable so podcast-only content can omit them.
     */
    public function up(): void
    {
        Schema::table('englo_contents', function (Blueprint $table) {
            $table->unsignedInteger('podcast_genre_id')->nullable()->after('language_id')->index();
        });

        Schema::table('englo_contents', function (Blueprint $table) {
            $table->unsignedInteger('genre_id')->nullable()->change();
            $table->unsignedInteger('language_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('englo_contents', function (Blueprint $table) {
            $table->dropColumn('podcast_genre_id');
        });

        Schema::table('englo_contents', function (Blueprint $table) {
            $table->unsignedInteger('genre_id')->nullable(false)->change();
            $table->unsignedInteger('language_id')->nullable(false)->change();
        });
    }
};
