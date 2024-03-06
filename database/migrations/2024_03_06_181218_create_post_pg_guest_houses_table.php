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
        Schema::create('post_pg_guest_houses', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('post_uuid')->nullable();
            $table->foreign('post_uuid')->references('uuid')->on('posts')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('furnishing')->nullable();
            $table->string('listed_by')->nullable();
            $table->integer('carpet_area')->nullable();
            $table->boolean('is_meal_included')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_pg_guest_houses');
    }
};
