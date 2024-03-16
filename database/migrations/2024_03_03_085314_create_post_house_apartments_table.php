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
            $table->uuid('id')->primary();
            $table->uuid('post_id');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->string('property_type');
            $table->integer('bedrooms');
            $table->string('furnishing');
            $table->string('construction_status');
            $table->string('listed_by');
            $table->integer('super_builtup_area')->nullable();
            $table->integer('carpet_area')->nullable();
            $table->decimal('monthly_maintenance', 10, 2)->nullable();
            $table->integer('total_floors')->nullable();
            $table->integer('floor_no')->nullable();
            $table->integer('car_parking')->nullable();
            $table->string('facing')->nullable();
            $table->string('project_name')->nullable();
            $table->string('title');
            $table->text('description');
            $table->decimal('amount', 10, 2);
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
