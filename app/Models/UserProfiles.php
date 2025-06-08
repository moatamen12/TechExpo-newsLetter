<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class UserProfiles extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'profile_id';

    protected $fillable = [
        'user_id', 'profile_photo', 'bio', 'work', 'website', 
        'social_links', 'followers_count', 'num_articles', 'reactions_count'
    ];
    
    //relation with the user model (one to one)
    public function user(){
        return $this->belongsTo(User::class , 'user_id','user_id');
    }

    //relation with the articles model (one to many)
    public function articles(){
        return $this->hasMany(Article::class,'author_id','user_id');
    }

    //relation with the comments model (one to many)
    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function get_profile_info($user_id)
    {
        $profile = UserProfiles::where('user_id', $user_id)->first();
    }

    /**
     * Get followers of this user profile
     */
    public function followers()
    {
        return $this->hasMany(userFollower::class, 'following_id', 'user_id');
    }
}
