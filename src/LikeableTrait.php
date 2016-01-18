<?php

namespace Conner\Likeable;

/**
 * Copyright (C) 2014 Robert Conner
 */
trait LikeableTrait
{
	/**
	 * DEPRECATED - Use whereLikedBy()
	 */
	public function scopeWhereLiked($query, $userId=null)
	{
		return $this->scopeWhereLikedBy($query, $userId);
	}
	
	/**
	 * Fetch records that are liked by a given user.
	 * Ex: Book::whereLikedBy(123)->get();
	 */
	public function scopeWhereLikedBy($query, $userId=null)
	{
		if(is_null($userId)) {
			$userId = $this->loggedInUserId();
		}
		
		return $query->whereHas('likes', function($q) use($userId) {
			$q->where('user_id', '=', $userId);
		});
	}
	
	
	/**
	 * Populate the $model->likes attribute
	 */
	public function getLikeCountAttribute()
	{
		return $this->likeCounter ? $this->likeCounter->count : 0;
	}
	
	/**
	 * Collection of the likes on this record
	 */
	public function likes()
	{
		return $this->morphMany(\Conner\Likeable\Like::class, 'likable');
	}

	/**
	 * Counter is a record that stores the total likes for the
	 * morphed record
	 */
	public function likeCounter()
	{
		return $this->morphOne(\Conner\Likeable\LikeCounter::class, 'likable');
	}
	
	/**
	 * Add a like for this record by the given user.
	 * @param $userId mixed - If null will use currently logged in user.
	 */
	public function like($userId=null)
	{
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
	public function unlike($userId=null)
	{
		if(is_null($userId)) {
			$userId = $this->loggedInUserId();
		}
		
		if($userId) {
			$like = $this->likes()
				->where('user_id', '=', $userId)
				->first();
	
			if(!$like) { return; }
	
			$like->delete();
		}

		$this->decrementLikeCount();
	}
	
	/**
	 * Has the currently logged in user already "liked" the current object
	 *
	 * @param string $userId
	 * @return boolean
	 */
	public function liked($userId=null)
	{
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
	private function incrementLikeCount()
	{
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
	private function decrementLikeCount()
	{
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
	 * @return number
	 */
	public function loggedInUserId()
	{
		return auth()->id();
	}
	
	/**
	 * Did the currently logged in user like this model
	 * Example : if($book->liked) { }
	 * @return boolean
	 */
	public function getLikedAttribute()
	{
		return $this->liked();
	}
	
}
