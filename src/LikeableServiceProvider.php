<?php namespace Conner\Likeable;

use Illuminate\Support\ServiceProvider;

/**
 * Copyright (C) 2015 Robert Conner
 */
class LikeableServiceProvider extends ServiceProvider {

	protected $defer = true;
	
	public function boot() {
		$this->publishes([
			__DIR__.'/../migrations/2014_09_10_065447_create_likeable_tables.php' => database_path('migrations/2014_09_10_065447_create_likeable_tables.php'),
		]);
	}
	
	public function register() {}

	public function when() {
		return array('artisan.start');
	}
	
}