Laravel Likeable Plugin
============

[![Build Status](https://travis-ci.org/rtconner/laravel-likeable.svg?branch=master)](https://travis-ci.org/rtconner/laravel-likeable)
[![Latest Stable Version](https://poser.pugx.org/rtconner/laravel-likeable/v/stable.svg)](https://packagist.org/packages/rtconner/laravel-likeable)
[![License](https://poser.pugx.org/rtconner/laravel-likeable/license.svg)](https://packagist.org/packages/rtconner/laravel-likeable)

Trait for Laravel Eloquent models to allow easy implementation of a "like" or "favorite" or "remember" feature.

#### Composer Install

    "require": {
        "rtconner/laravel-likeable": "0.1.*"
    }

#### Run the migrations

	php artisan migrate --package=rtconner/laravel-likeable
	
#### Setup your models

    class Article extends \Eloquent {
        use Conner\Likeable\LikeableTrait;
    }

#### Sample Usage

    $article->like(); // like the article for current user
    $article->like($myUserId); // pass in your own user id
    $article->like(0); // just add likes to the count, and don't track by user
    
    $article->unlike(); // remove like from the article
    $article->unlike($myUserId); // pass in your own user id
    $article->unlike(0); // remove likes from the count -- does not check for user
    
    $article->likes; // get count of likes

    $article->liked(); // check if currently logged in user liked the article
    $article->liked($myUserId);
    
    Article::whereLiked($myUserId) // find only articles where user liked them
    	->with('likeCounter') // highly suggested to allow eager load
    	->get();
    
#### Credits

 - Robert Conner - http://smartersoftware.net