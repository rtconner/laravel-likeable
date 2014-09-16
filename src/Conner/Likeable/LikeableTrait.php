<?php namespace Conner\Likeable;

trait LikeableTrait {

	/**
	 * Fetch only records that currently logged in user has liked/followed
	 */
	public function scopeWhereLiked($query) {
		return $query->whereHas('likeCollection', function($q) {
			$q->where('user_id', '=', user('id'));
		});
	}
	
	public function getLikedAttribute() {
		return (bool) $this->likeCollection()->where('user_id', '=', user('id'))->count();
	}
	
	public function likeCollection() {
		return $this->morphMany('\Conner\Likeable\Liked', 'likable');
	}

	public function likeCount() {
		return $this->morphMany('\Conner\Likeable\LikeCount', 'likable');
	}

	public function like() {
		$liked = $this->likeCollection()->where('user_id', '=', user('id'))->first();

		if($liked)
			return true;

		$liked = new Liked();
		$liked->user_id = user('id');
		$this->likeCollection()->save($liked);

		$likeCount = $this->likeCount()->first();

		if($likeCount) {

			$likeCount->count++;
			$likeCount->save();

		} else {
			
			$likeCount = new LikeCount();
			$likeCount->count = 1;
			$this->likeCount()->save($likeCount);
			
		}
	}

	public function unlike() {
		$liked = $this->likeCollection()->where('user_id', '=', user('id'))->first();

		if(!$liked)
			return true;

		$liked->delete();

		$likeCount = $this->likeCount()->first();

		if($likeCount) {
			$likeCount->count--;
			$likeCount->save();
		}
	}

	public function likes() {
		$likeCount = $this->likeCount()->first();

		if(!$likeCount) {
			return 0;
		}

		return $likeCount->count;
	}
	
}
