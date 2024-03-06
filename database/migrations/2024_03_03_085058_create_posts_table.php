<?php

use App\Enums\PostStatus;
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
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->uuid('user_uuid')->nullable();
            $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->text('address', 250);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('type', 5);
            $table->enum('status', array_column(PostStatus::cases(), 'value'))->default(PostStatus::Pending->value);
            $table->timestamp('post_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
