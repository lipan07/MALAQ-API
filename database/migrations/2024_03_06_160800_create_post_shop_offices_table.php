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
        Schema::create('post_shop_offices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->string('furnishing')->nullable();
            $table->string('listed_by')->nullable();
            $table->integer('super_builtup_area')->nullable();
            $table->integer('carpet_area')->nullable();
            $table->decimal('monthly_maintenance', 10, 2)->nullable();
            $table->integer('car_parking')->nullable();
            $table->string('washroom')->nullable();
            $table->string('project_name')->nullable();
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
        Schema::dropIfExists('post_shop_offices');
    }
};
