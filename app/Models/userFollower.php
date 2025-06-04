<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class userFollower extends Model
{
    protected $table = 'user_followers';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Get the user that is following
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id', 'user_id');
    }

    /**
     * Get the user being followed
     */
    public function followed()
    {
        return $this->belongsTo(User::class, 'followed_id', 'user_id');
    }

    /**
     * Get the writers that this user follows
     * 
     * @param int $userId The ID of the user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function scopegetFollowedWriters($quiry,$userId)
    {
        return $quiry->where('follower_id', $userId)
            ->whereHas('followed', function($query) {
                $query->where('role', 'author');
            })
            ->with(['followed' => function($query) {
                $query->select('user_id', 'name')
                    ->with('profile');
            }])
            ->get();
    }

    /**
     * Get the followers of this user
     * 
     * @param int $userId The ID of the user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function scopegetFollowers($quiry,$userId)
    {
        return $quiry->where('followed_id', $userId)
            ->with(['follower' => function($query) {
                $query->select('user_id', 'name');
            }])
            ->get();
    }

}
