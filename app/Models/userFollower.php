<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserProfiles;

class userFollower extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_followers'; // Make sure this matches your actual table name

    /**
     * Indicates if the model should be timestamped.
     * Set to false since we only have created_at
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'follower_id', // Foreign key for the user who is following
        'following_id', // Foreign key for the user who is being followed
        'created_at', // Manual created_at handling
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    //relation with the user model "reader user" the one who follow "follower"
    public function follower(){
        return $this->belongsTo(User::class,'follower_id','user_id');
    }
    
    //relation with the userProfile model "authore user" the one being followed "following" 
    public function following(){
        return $this->belongsTo(UserProfiles::class, 'following_id', 'profile_id');
    }

    /**
     * Scope a query to only include followed writers for a given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId The ID of the user who is following.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetFollowedWriters($query, $userId)
    {
        return $query->where('follower_id', $userId)
                     ->with('following');
    }

    /**
     * Scope to get recent followers for a user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId The ID of the user being followed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecentFollowers($query, $userId)
    {
        return $query->where('following_id', $userId)
                     ->with('follower')
                     ->orderBy('created_at', 'desc');
    }
}
