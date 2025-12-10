<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, ensure all users have an email (set a default if needed)
        // This is a safety check - you may want to handle existing NULL emails differently
        DB::table('users')
            ->whereNull('email')
            ->update(['email' => DB::raw("CONCAT('user_', id, '@temp.com')")]);

        Schema::table('users', function (Blueprint $table) {
            // Make email required (NOT NULL)
            $table->string('email', 50)->nullable(false)->change();
        });

        // Drop unique constraint on phone_no if it exists
        // Check if unique index exists before dropping
        $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_phone_no_unique'");
        if (!empty($indexes)) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['phone_no']);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            // Make phone_no nullable
            $table->string('phone_no', 15)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert email to nullable
            $table->string('email', 50)->nullable()->change();

            // Revert phone_no to NOT NULL and add unique constraint
            $table->string('phone_no', 15)->nullable(false)->change();
            $table->unique('phone_no');
        });
    }
};
