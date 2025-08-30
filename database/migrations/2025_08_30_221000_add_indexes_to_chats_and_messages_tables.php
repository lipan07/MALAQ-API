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
        // Chats table indexes
        Schema::table('chats', function (Blueprint $table) {
            $table->index('updated_at');
            $table->unique(['buyer_id', 'seller_id', 'post_id'], 'chat_buyer_seller_post_unique');
        });

        // Messages table indexes
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['chat_id', 'created_at'], 'messages_chat_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex(['updated_at']);
            $table->dropUnique('chat_buyer_seller_post_unique');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_chat_created_idx');
        });
    }
};
