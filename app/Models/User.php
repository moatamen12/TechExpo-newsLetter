<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';



    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //relation with userProfile model "relation one to one"
    public function userProfile()
    {
        return $this->hasOne(UserProfiles::class, 'user_id', 'user_id');
    }

    
    /**
     * Scope a query to only include users who are writers (authors or admins).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array|string $writerRoles The role(s) that identify a writer. Defaults to 'author'.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsWriter($query, $writerRoles = 'author')
    {
        if (is_array($writerRoles)) {
            return $query->whereIn('role', $writerRoles);
        }
        return $query->where('role', $writerRoles);
    }

    /**
     * Check if the user is a writer (author or admin) (instance method).
     *
     * @param  array|string $writerRoles
     * @return bool
     */
    public function isWriterInstance($writerRoles = 'author'): bool
    {
        if (is_array($writerRoles)) {
            return in_array($this->role, $writerRoles);
        }
        return $this->role === $writerRoles;
    }
    
    //relation with the contact model "relation one to many"
    public function contact()
    {
        return $this->hasMany(Contact::class, 'user_id', 'user_id');
    }
    //relation with the comment model "relation one to many"
    public function comment()
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }
    
    public function articleLikes()
    {
        return $this->hasMany(ArticleLike::class);
    }

    //relation with the userSavedArticle model "relation one to many"
    public function userSavedArticle()
    {
        return $this->hasMany(userSavedArticle::class, 'user_id', 'user_id');
    }

    /**
     * The users that this user is following (writers).
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_followers', 'follower_id', 'following_id');

    }

    /**
     * Check if the current user is following another user.
     */
    public function isFollowing($profile)
    {
        // If it's a UserProfile object, get the profile_id
        if (is_object($profile) && isset($profile->profile_id)) {
            $profileId = $profile->profile_id;
        } else {
            $profileId = $profile;
        }
        
        return \App\Models\userFollower::where('follower_id', $this->user_id)
                           ->where('following_id', $profileId)
                           ->exists();
    }

    /**
     * Follow a user.
     */
    public function follow($userId)
    {
        if (!$this->isFollowing($userId)) {
            $this->following()->attach($userId);
            return true;
        }
        return false;
    }

    /**
     * Unfollow a user.
     */
    public function unfollow($userId)
    {
        return $this->following()->detach($userId);
    }

    /**
     * Check if user has liked an article
     */
    public function hasLiked($article)
    {
        return \App\Models\ArticleLike::where('user_id', $this->user_id)
                           ->where('article_id', $article->article_id)
                           ->exists();
    }


    /**
     * Check if user has saved an article
     */
    public function hasSaved($article)
    {
        return \App\Models\userSavedArticle::where('user_id', $this->user_id)
                           ->where('article_id', $article->article_id)
                           ->exists();
    }
}
