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
    ];

    //relation with the user model "reader user" the one who follow "follower"
    public function follower(){
        return $this -> belongsto(User::class,'follower_id','user_id');
    }
    
    //relation with the userProfile model "authore user" the one being followed "following" 
    public function following(){
        return $this -> belongsto(UserProfiles::class, 'following_id', 'profile_id');
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
}
