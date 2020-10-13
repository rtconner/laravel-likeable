<?php

namespace Conner\Likeable;

use Illuminate\Support\ServiceProvider;

/**
 * Copyright (C) 2015 Robert Conner
 */
class LikeableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
    
    public function register()
    {
    }
}
