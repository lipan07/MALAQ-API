<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('post_id')->constrained('posts')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->default('qr_code');
            $table->string('screenshot_path')->nullable();
            $table->string('status', 20)->default('pending'); // pending, confirmed, rejected
            $table->timestamp('admin_verified_at')->nullable();
            $table->foreignUuid('admin_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'post_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
