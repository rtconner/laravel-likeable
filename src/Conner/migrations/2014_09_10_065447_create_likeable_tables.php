<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLikeableTables extends Migration {

	public function up() {
		
		Schema::create('likeable_liked', function(Blueprint $table) {
			$table->increments('id');
			$table->string('likable_id', 36);
			$table->string('likable_type', 255);
			$table->string('user_id', 36);
			$table->timestamps();
		});
		
		Schema::create('likeable_liked_count', function(Blueprint $table) {
			$table->increments('id');
			$table->string('likable_id', 36);
			$table->string('likable_type', 255);
			$table->integer('count');
		});
		
	}

	public function down() {
		Schema::drop('likeable_liked');
		Schema::drop('likeable_liked_count');
	}
}