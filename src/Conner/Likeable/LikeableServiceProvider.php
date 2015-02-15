<?php namespace Conner\Likeable;

use Illuminate\Support\ServiceProvider;

/**
 * Copyright (C) 2015 Robert Conner
 */
class LikeableServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 */
	protected $defer = false;
	
	/**
	 * Bootstrap the application events.
	 */
	public function boot() {
		$this->publishes([
			__DIR__.'/../../../migrations/2014_09_10_065447_create_likeable_tables.php' => base_path('database/migrations/2014_09_10_065447_create_likeable_tables.php'),
		]);
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {}

}