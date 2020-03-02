<?php

namespace Conner\Likeable;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * @mixin \Eloquent
 * @property Likeable likeable
 */
class Like extends Eloquent
{
	protected $table = 'likeable_likes';
	public $timestamps = true;
	protected $fillable = ['likeable_id', 'likeable_type', 'user_id'];

    /**
     * @access private
     */
	public function likeable()
	{
		return $this->morphTo();
	}
}