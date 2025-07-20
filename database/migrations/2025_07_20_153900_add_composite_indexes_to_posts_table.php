<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompositeIndexesToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Composite index for location queries
            $table->index(['latitude', 'longitude'], 'posts_lat_long_index');

            // Composite index for category + location queries
            $table->index(['category_id', 'latitude', 'longitude'], 'posts_category_lat_long_index');

            // Composite index for search + location queries
            $table->index(['title', 'latitude', 'longitude'], 'posts_title_lat_long_index');

            // Optional: Composite index for price + location queries
            $table->index(['type', 'latitude', 'longitude'], 'posts_type_lat_long_index');

            $table->index(['status', 'latitude', 'longitude'], 'posts_status_lat_long_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_lat_long_index');
            $table->dropIndex('posts_category_lat_long_index');
            $table->dropIndex('posts_title_lat_long_index');
            $table->dropIndex('posts_type_lat_long_index');
            $table->dropIndex('posts_status_lat_long_index');
        });
    }
}
