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
        Schema::create('post_cars', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->first();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('brand')->nullable();
            $table->year('year')->nullable();
            $table->string('fuel')->nullable();
            $table->string('transmission')->nullable();
            $table->integer('km_driven')->nullable();
            $table->integer('no_of_owner')->nullable();
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
        Schema::dropIfExists('post_cars');
    }
};
