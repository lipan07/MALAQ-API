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
        Schema::create('post_jobs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('post_uuid')->nullable();
            $table->foreign('post_uuid')->references('uuid')->on('posts')->onDelete('cascade');
            $table->string('salary_period', 20);
            $table->string('position_type', 20);
            $table->decimal('salary_from', 10, 2);
            $table->decimal('salary_to', 10, 2)->nullable();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_jobs');
    }
};
