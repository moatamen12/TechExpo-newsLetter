<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class userFollower extends Model
{
    // Ensure this points to your actual table name for follows
    // If your table is named 'user_following', uncomment and set this:
    // protected $table = 'user_following'; 
    // If it's 'user_followers', this line can be omitted if the model name is UserFollower,
    // but since it's userFollower (lowercase u), explicitly setting it is safer.
    protected $table = 'user_followers'; // Assuming the error message's table name is correct

    public $timestamps = false; // Set to true if you add created_at/updated_at to the pivot table

    protected $fillable = [
        'follower_id', // The user doing the following
        'following_id', // The user being followed
        // 'created_at', // if you add timestamps
    ];

    /**
     * Get the user who is being followed.
     */
    public function followed()
    {
        // This tells Eloquent that the 'following_id' column in the current model's table
        // (user_followers) links to the 'user_id' column in the 'users' table.
        return $this->belongsTo(User::class, 'following_id', 'user_id');
    }

    /**
     * Get the user who is the follower.
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id', 'user_id');
    }

    /**
     * Scope a query to only include followed users who are authors.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId The ID of the user whose followed writers are to be fetched.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetFollowedWriters(Builder $query, $userId): Builder
    {
        return $query->where('follower_id', $userId) // Current user is the follower
                     ->whereHas('followed', function (Builder $subQuery) {
                         // Check the 'role' on the related 'User' model (the one being followed)
                         $subQuery->where('role', 'author');
                     })
                     ->with(['followed' => function ($query) {
                        // Eager load the 'followed' User model and their profile
                        $query->with('userProfile');
                     }]);
    }
}
