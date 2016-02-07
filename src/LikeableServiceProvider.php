<?php

namespace Conner\Likeable;

use Illuminate\Support\ServiceProvider;

/**
 * Copyright (C) 2015 Robert Conner
 */
class LikeableServiceProvider extends ServiceProvider
{
	protected $defer = true;
	
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../migrations/' => database_path('migrations')
		], 'migrations');
	}
	
	public function register() {}
}