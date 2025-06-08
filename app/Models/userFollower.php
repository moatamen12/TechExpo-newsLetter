<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class userFollower extends Model
{
    protected $table = 'user_followers'; // Changed to the correct table name
    
    public $timestamps = false; 
    
    protected $fillable = [
        'follower_id',
        'following_id'
    ];

    /**
     * Get the user who is being followed (the writer).
     */
    public function followed()
    {
        return $this->belongsTo(UserProfiles::class, 'following_id', 'profile_id');
    }

    /**
     * Get the user who is the follower (the reader).
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id', 'user_id');
    }
}
