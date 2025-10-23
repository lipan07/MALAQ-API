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
        Schema::table('posts', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index(['status', 'post_time'], 'posts_status_post_time_index');
            $table->index(['user_id', 'status'], 'posts_user_status_index');
            $table->index(['category_id', 'status', 'post_time'], 'posts_category_status_time_index');
            $table->index(['type', 'status'], 'posts_type_status_index');
            $table->index(['amount', 'status'], 'posts_amount_status_index');
        });

        Schema::table('chats', function (Blueprint $table) {
            // Add indexes for chat queries
            $table->index(['buyer_id', 'updated_at'], 'chats_buyer_updated_index');
            $table->index(['seller_id', 'updated_at'], 'chats_seller_updated_index');
            $table->index(['post_id', 'updated_at'], 'chats_post_updated_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            // Add indexes for message queries
            $table->index(['chat_id', 'created_at'], 'messages_chat_created_index');
            $table->index(['user_id', 'is_seen'], 'messages_user_seen_index');
        });

        Schema::table('users', function (Blueprint $table) {
            // Add indexes for user queries
            $table->index(['status', 'last_activity'], 'users_status_activity_index');
            $table->index(['created_at'], 'users_created_index');
        });

        Schema::table('images', function (Blueprint $table) {
            // Add indexes for image queries
            $table->index(['imageable_type', 'imageable_id'], 'images_polymorphic_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_post_time_index');
            $table->dropIndex('posts_user_status_index');
            $table->dropIndex('posts_category_status_time_index');
            $table->dropIndex('posts_type_status_index');
            $table->dropIndex('posts_amount_status_index');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex('chats_buyer_updated_index');
            $table->dropIndex('chats_seller_updated_index');
            $table->dropIndex('chats_post_updated_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_chat_created_index');
            $table->dropIndex('messages_user_seen_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_status_activity_index');
            $table->dropIndex('users_created_index');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex('images_polymorphic_index');
        });
    }
};
