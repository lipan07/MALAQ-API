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
        Schema::create('post_houses_apartments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('post_uuid')->nullable();
            $table->foreign('post_uuid')->references('uuid')->on('posts')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->string('furnishing')->nullable();
            $table->string('construction_status')->nullable();
            $table->string('listed_by')->nullable();
            $table->integer('super_builtup_area')->nullable();
            $table->integer('carpet_area')->nullable();
            $table->decimal('monthly_maintenance', 10, 2)->nullable();
            $table->integer('total_floors')->nullable();
            $table->integer('floor_no')->nullable();
            $table->string('car_parking')->nullable();
            $table->string('facing')->nullable();
            $table->string('project_name')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_houses_apartments');
    }
};
