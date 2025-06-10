<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Auth;

class UserProfiles extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'profile_id';

    protected $fillable = [
        'user_id', 'profile_photo', 'bio', 'work', 'website', 
        'social_links', 'followers_count', 'num_articles', 'reactions_count', 'title'
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

    /**
     * Relationship with SocialLink model (one to many)
     */
    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class, 'user_id', 'user_id');
    }

    /**
     * Get active social links
     */
    public function activeSocialLinks()
    {
        return $this->hasMany(SocialLink::class, 'user_id', 'user_id')->active();
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
