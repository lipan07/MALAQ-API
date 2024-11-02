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
        Schema::create('post_heavy_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->string('condition');
            $table->integer('km_driven');
            $table->string('fuel_type');
            $table->decimal('price', 10, 2);
            $table->string('description', 255)->nullable();
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_heavy_vehicles');
    }
};
