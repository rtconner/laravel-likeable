Laravel Likeable Plugin
============

Important Note: As of version 1.2 I renamed `Conner\Likeable\LikeableTrait` to `Conner\Likeable\Likeable`

[![Build Status](https://travis-ci.org/rtconner/laravel-likeable.svg?branch=laravel-7)](https://travis-ci.org/rtconner/laravel-likeable)
[![Latest Stable Version](https://poser.pugx.org/rtconner/laravel-likeable/v/stable.svg)](https://packagist.org/packages/rtconner/laravel-likeable)
[![License](https://poser.pugx.org/rtconner/laravel-likeable/license.svg)](https://packagist.org/packages/rtconner/laravel-likeable)

Trait for Laravel Eloquent models to allow easy implementation of a "like" or "favorite" or "remember" feature.

[Laravel 5/6/7 Documentation](https://github.com/rtconner/laravel-likeable/tree/laravel-7)  
[Laravel 4 Documentation](https://github.com/rtconner/laravel-likeable/tree/laravel-4)

#### Composer Install (for Laravel 5)

	composer require rtconner/laravel-likeable "~3.0"

#### Then run the migrations

```bash
php artisan migrate
```

#### Setup your models

```php
class Article extends \Illuminate\Database\Eloquent\Model {
	use \Conner\Likeable\LikeableTrait;
}
```

#### Sample Usage

```php
$article->like(); // like the article for current user
$article->like($myUserId); // pass in your own user id
$article->like(0); // just add likes to the count, and don't track by user

$article->unlike(); // remove like from the article
$article->unlike($myUserId); // pass in your own user id
$article->unlike(0); // remove likes from the count -- does not check for user

$article->likeCount; // get count of likes

$article->likes; // Iterable Illuminate\Database\Eloquent\Collection of existing likes 

$article->liked(); // check if currently logged in user liked the article
$article->liked($myUserId);

Article::whereLikedBy($myUserId) // find only articles where user liked them
	->with('likeCounter') // highly suggested to allow eager load
	->get();
```

#### Credits

 - Robert Conner - http://smartersoftware.net
