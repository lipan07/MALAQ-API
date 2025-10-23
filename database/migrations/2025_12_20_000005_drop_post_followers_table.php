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
        Schema::dropIfExists('post_followers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate post_followers table if needed for rollback
        Schema::create('post_followers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('post_id');
            $table->uuid('post_user_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('post_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
            $table->unique(['user_id', 'post_id'], 'unique_follow');
        });
    }
};
