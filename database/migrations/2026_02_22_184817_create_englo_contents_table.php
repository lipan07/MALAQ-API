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
        Schema::create('englo_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('genre_id')->index();
            $table->unsignedInteger('language_id')->index();
            $table->string('url')->nullable()->index();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('englo_contents');
    }
};
