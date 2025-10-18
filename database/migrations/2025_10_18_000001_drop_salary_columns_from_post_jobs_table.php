<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('post_jobs')) {
            Schema::table('post_jobs', function (Blueprint $table) {
                if (Schema::hasColumn('post_jobs', 'salary_from')) {
                    $table->dropColumn('salary_from');
                }
                if (Schema::hasColumn('post_jobs', 'salary_to')) {
                    $table->dropColumn('salary_to');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('post_jobs')) {
            Schema::table('post_jobs', function (Blueprint $table) {
                if (!Schema::hasColumn('post_jobs', 'salary_from')) {
                    $table->decimal('salary_from', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('post_jobs', 'salary_to')) {
                    $table->decimal('salary_to', 10, 2)->nullable();
                }
            });
        }
    }
};
