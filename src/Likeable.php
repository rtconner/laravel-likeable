<?php

namespace Conner\Likeable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Copyright (C) 2014 Robert Conner
 *
 * @method static Builder whereLikedBy($userId=null)
 * @property Collection|Like[] likes
 * @property Liked liked
 * @property integer likeCount
 */
trait Likeable
{
    public static function bootLikeable()
    {
        if (static::removeLikesOnDelete()) {
            static::deleting(function ($model) {
                /** @var Likeable $model */
                $model->removeLikes();
            });
        }
    }
    
    /**
     * Populate the $model->likes attribute
     */
    public function getLikeCountAttribute()
    {
        return $this->likeCounter ? $this->likeCounter->count : 0;
    }

    /**
     * Add a like for this record by the given user.
     * @param $userId mixed - If null will use currently logged in user.
     */
    public function like($userId=null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }
        
        if ($userId) {
            $like = $this->likes()
                ->where('user_id', '=', $userId)
                ->first();
    
            if ($like) {
                return;
            }
    
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
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }
        
        if ($userId) {
            $like = $this->likes()
                ->where('user_id', '=', $userId)
                ->first();
    
            if (!$like) {
                return;
            }
    
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
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }
        
        return (bool) $this->likes()
            ->where('user_id', '=', $userId)
            ->count();
    }
    
    /**
     * Should remove likes on model row delete (defaults to true)
     * public static removeLikesOnDelete = false;
     */
    public static function removeLikesOnDelete()
    {
        return isset(static::$removeLikesOnDelete)
            ? static::$removeLikesOnDelete
            : true;
    }
    
    /**
     * Delete likes related to the current record
     */
    public function removeLikes()
    {
        $this->likes()->delete();
        $this->likeCounter()->delete();
    }


    /**
     * Collection of the likes on this record
     * @access private
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Did the currently logged in user like this model
     * Example : if($book->liked) { }
     * @return boolean
     * @access private
     */
    public function getLikedAttribute()
    {
        return $this->liked();
    }

    /**
     * Counter is a record that stores the total likes for the
     * morphed record
     * @access private
     */
    public function likeCounter()
    {
        return $this->morphOne(LikeCounter::class, 'likeable');
    }

    /**
     * Private. Increment the total like count stored in the counter
     */
    private function incrementLikeCount()
    {
        $counter = $this->likeCounter()->first();

        if ($counter) {
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

        if ($counter) {
            $counter->count--;
            if ($counter->count) {
                $counter->save();
            } else {
                $counter->delete();
            }
        }
    }


    /**
     * Fetch records that are liked by a given user.
     * Ex: Book::whereLikedBy(123)->get();
     * @access private
     */
    public function scopeWhereLikedBy($query, $userId=null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return $query->whereHas('likes', function ($q) use ($userId) {
            $q->where('user_id', '=', $userId);
        });
    }

    /**
     * Fetch the primary ID of the currently logged in user
     * @return mixed
     */
    private function loggedInUserId()
    {
        return auth()->id();
    }
}
