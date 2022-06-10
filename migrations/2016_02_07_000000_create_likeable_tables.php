<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikeableTables extends Migration
{
    public function up()
    {
        Schema::create('likeable_likes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuidMorphs('likeable');
            $table->string('user_id', 36)->index();
            $table->timestamps();
            $table->unique(['likeable_id', 'likeable_type', 'user_id'], 'likeable_likes_unique');
        });

        Schema::create('likeable_like_counters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuidMorphs('likeable');
            $table->unsignedBigInteger('count')->default(0);
            $table->unique(['likeable_id', 'likeable_type'], 'likeable_counts');
        });
    }

    public function down()
    {
        Schema::drop('likeable_likes');
        Schema::drop('likeable_like_counters');
    }
}
