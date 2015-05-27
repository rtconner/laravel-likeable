<?php namespace Conner\Likeable;

class Like extends Illuminate\Database\Eloquent\Model {

	protected $table = 'likeable_likes';
	public $timestamps = true;
	protected $fillable = ['likable_id', 'likable_type', 'user_id'];

	public function likable()
	{
		return $this->morphTo();
	}
	
}