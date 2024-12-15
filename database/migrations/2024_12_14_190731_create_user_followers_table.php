<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFollowersTable extends Migration
{
    public function up()
    {
        Schema::create('user_followers', function (Blueprint $table) {
            $table->uuid('follower_id');
            $table->uuid('following_id');
            $table->timestamps();

            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('following_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['follower_id', 'following_id'], 'unique_follow');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_followers');
    }
}
