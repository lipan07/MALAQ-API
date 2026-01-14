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
        Schema::create('invite_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); // User who owns this token
            $table->string('token', 7)->unique(); // 7-digit unique token
            $table->uuid('used_by_user_id')->nullable(); // User who used this token (null if unused)
            $table->timestamp('expires_at'); // Token expiration (24 hours)
            $table->timestamp('used_at')->nullable(); // When token was used
            $table->boolean('is_used')->default(false); // Whether token has been used
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('used_by_user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('token');
            $table->index('user_id');
            $table->index('is_used');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invite_tokens');
    }
};
