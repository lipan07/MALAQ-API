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
        Schema::table('company_details', function (Blueprint $table) {
            $table->string('contact_person_name', 100)->nullable()->after('website');
            $table->string('contact_person_role', 100)->nullable()->after('contact_person_name');
            $table->string('contact_person_email', 100)->nullable()->after('contact_person_role');
            $table->string('contact_person_phone', 100)->nullable()->after('contact_person_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn([
                'contact_person_name',
                'contact_person_role',
                'contact_person_email',
                'contact_person_phone'
            ]);
        });
    }
};
