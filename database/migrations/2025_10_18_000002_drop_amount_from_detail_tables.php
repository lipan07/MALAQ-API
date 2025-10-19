<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $tables = [
        'post_mobiles',
        'post_houses_apartments',
        'post_vehicle_spare_parts',
        'post_heavy_machineries',
        'post_land_plots',
        'post_bikes',
        'post_electronics_appliances',
        'post_services',
        'post_others',
        'post_furniture',
        'post_fashions',
        'post_books',
        'post_accessories',
        'post_shop_offices',
        'post_pg_guest_houses',
        'post_sport_hobbies',
        'post_pets',
        'post_cars',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'amount')) {
                // 1) Copy existing amounts into posts.amount where missing
                // Assumes detail tables have a post_id column
                try {
                    DB::statement(
                        "UPDATE posts p 
                         INNER JOIN {$tableName} t ON t.post_id = p.id 
                         SET p.amount = COALESCE(p.amount, CAST(t.amount AS DECIMAL(15,2))) 
                         WHERE t.amount IS NOT NULL 
                           AND (p.amount IS NULL OR p.amount = 0)"
                    );
                } catch (\Throwable $e) {
                    // Fallback without CAST (if driver differs)
                    try {
                        DB::statement(
                            "UPDATE posts p 
                             INNER JOIN {$tableName} t ON t.post_id = p.id 
                             SET p.amount = COALESCE(p.amount, t.amount) 
                             WHERE t.amount IS NOT NULL 
                               AND (p.amount IS NULL OR p.amount = 0)"
                        );
                    } catch (\Throwable $ignored) {
                        // Ignore copy failure to not block migration; admin can backfill manually
                    }
                }

                // 2) Drop the amount column from detail table
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropColumn('amount');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'amount')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->decimal('amount', 15, 2)->nullable();
                });
            }
        }
    }
};
