<?php

namespace Conner\Likeable;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Like extends Eloquent
{
	protected $table = 'likeable_likes';
	public $timestamps = true;
	protected $fillable = ['likable_id', 'likable_type', 'user_id'];

	public function likable()
	{
		return $this->morphTo();
	}
	
}