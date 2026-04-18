<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->timestamp('buyer_deleted_at')->nullable()->after('updated_at');
            $table->timestamp('seller_deleted_at')->nullable()->after('buyer_deleted_at');
        });

        if (Schema::hasColumn('chats', 'deleted_at')) {
            DB::table('chats')
                ->whereNotNull('deleted_at')
                ->update([
                    'buyer_deleted_at' => DB::raw('deleted_at'),
                    'seller_deleted_at' => DB::raw('deleted_at'),
                ]);

            Schema::table('chats', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('updated_at');
        });

        DB::table('chats')
            ->whereNotNull('buyer_deleted_at')
            ->whereNotNull('seller_deleted_at')
            ->update([
                'deleted_at' => DB::raw('GREATEST(buyer_deleted_at, seller_deleted_at)'),
            ]);

        DB::table('chats')
            ->whereNotNull('buyer_deleted_at')
            ->whereNull('seller_deleted_at')
            ->update(['deleted_at' => DB::raw('buyer_deleted_at')]);

        DB::table('chats')
            ->whereNotNull('seller_deleted_at')
            ->whereNull('buyer_deleted_at')
            ->update(['deleted_at' => DB::raw('seller_deleted_at')]);

        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['buyer_deleted_at', 'seller_deleted_at']);
        });
    }
};
