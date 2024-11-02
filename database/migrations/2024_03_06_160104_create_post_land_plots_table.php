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
        Schema::create('post_land_plots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->string('listed_by', 50)->nullable();
            $table->integer('carpet_area');
            $table->integer('length')->nullable();
            $table->integer('breadth')->nullable();
            $table->string('facing', 30)->nullable();
            $table->string('project_name', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_land_plots');
    }
};
