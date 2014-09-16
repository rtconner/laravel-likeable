<?php namespace Conner\Likeable;

class Liked extends \Eloquent {

	protected $table = 'likeable_liked';
	public $timestamps = true;
	protected $fillable = ['likable_id', 'likable_type', 'user_id'];

	public function likable() {
		return $this->morphTo();
	}
	
}