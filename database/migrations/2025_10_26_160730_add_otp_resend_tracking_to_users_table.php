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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('otp_resend_count')->default(0)->after('otp');
            $table->timestamp('otp_sent_at')->nullable()->after('otp_resend_count');
            $table->timestamp('last_otp_resend_at')->nullable()->after('otp_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_resend_count', 'otp_sent_at', 'last_otp_resend_at']);
        });
    }
};
