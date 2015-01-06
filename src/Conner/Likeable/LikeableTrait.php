<?php namespace Conner\Likeable;

/**
 * Copyright (C) 2014 Robert Conner
 */

trait LikeableTrait {
	protected $appends = array('liked');
	public $liked = false;
	
	/**
	 * Fetch only records that currently logged in user has liked/followed
	 */
	public function scopeWhereLiked($query, $userId=null) {
		if(is_null($userId)) {
			$userId = $this->loggedInUserId();
		}
		
		return $query->whereHas('likes', function($q) use($userId) {
			$q->where('user_id', '=', $userId);
		});
	}

	/**
	 * Fetch likes for collection
	 */
	public function scopeWithLikedBy($query, $userId) {
    return $query->with(array('likes' => function($query) use (&$userId)
    {
        $query->where('user_id',$userId );
    }));
  }
	
	/**
	 * Populate the $model->likes attribute
	 */
	public function getLikesAttribute() {
		return $this->likeCounter ? $this->likeCounter->count : 0;
	}
	
	/**
	 * Collection of the likes on this record
	 */
	public function likes() {
		return $this->morphMany('\Conner\Likeable\Like', 'likable');
	}

	/**
	 * Counter is a record that stores the total likes for the
	 * morphed record
	 */
	public function likeCounter() {
		return $this->morphOne('\Conner\Likeable\LikeCounter', 'likable');
	}
	
	/**
	 * Add a like for this record by the given user.
	 * @param $userId mixed - If null will use currently logged in user.
	 */
	public function like($userId=null) {
		if(is_null($userId)) {
			$userId = $this->loggedInUserId();
		}
		
		if($userId) {
			$like = $this->likes()
				->where('user_id', '=', $userId)
				->first();
	
			if($like) return;
	
			$like = new Like();
			$like->user_id = $userId;
			$this->likes()->save($like);
		}

		$this->incrementLikeCount();
	}

	/**
	 * Remove a like from this record for the given user.
	 * @param $userId mixed - If null will use currently logged in user.
	 */
	public function unlike($userId=null) {
		if(is_null($userId)) {
			$userId = $this->loggedInUserId();
		}
		
		if($userId) {
			$like = $this->likes()
				->where('user_id', '=', $userId)
				->first();
	
			if(!$like) return;
	
			$like->delete();
		}

		$this->decrementLikeCount();
	}

	/**
	 * Toggle a like
	 * @param $userId mixed - If null will use currently logged in user.
	 */
	public function toggle($userId=null) {

		if(is_null($userId)) {
			$userId = $this->loggedInUserId();
		}

		if($this->liked($userId)){
			$this->unlike($userId);
		}else{
			$this->like($userId);
		}
		return $this;
	}
	
	public function liked($userId=null) {
		if(is_null($userId)) {
			$userId = $this->loggedInUserId();
		}
		
		return (bool) $this->likes()
			->where('user_id', '=', $userId)
			->count();
	}
	
	/**
	 * Private. Increment the total like count stored in the counter
	 */
	private function incrementLikeCount() {
		
		$counter = $this->likeCounter()->first();
		
		if($counter) {
			
			$counter->count++;
			$counter->save();
			
		} else {
			
			$counter = new LikeCounter;
			$counter->count = 1;
			$this->likeCounter()->save($counter);
			
		}
	}
	
	/**
	 * Private. Decrement the total like count stored in the counter
	 */
	private function decrementLikeCount() {
		$counter = $this->likeCounter()->first();

		if($counter) {
			$counter->count--;
			if($counter->count) {
				$counter->save();
			} else {
				$counter->delete();
			}
		}
	}
	
	/**
	 * Fetch the primary ID of the currently logged in user
	 * Update: This is disabled. Cookieless sessions only allowed
	 * @return number
	 */
	public function loggedInUserId() {
		
		if(\App::environment()=='testing') {
			return 1;
		}

		throw new \Exception("Not Authorized: You must pass in a user for this to work", 500);
		//return \Auth::id();
	}

	/**
   * @return boolean
   */
  public function getLikedAttribute() {
      if(count($this->likes)) {
          return true;
      } else {
          return false;
      }
      unset($this->liked);
  }
	
}
