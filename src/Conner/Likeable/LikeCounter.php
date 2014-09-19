<?php namespace Conner\Likeable;

class LikeCounter extends \Eloquent {

	protected $table = 'likeable_like_counters';
	public $timestamps = false;
	protected $fillable = ['likable_id', 'likable_type', 'count'];
	
	public function likable() {
		return $this->morphTo();
	}
	
}