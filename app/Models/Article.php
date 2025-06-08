<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Categorie; // Add this import

class Article extends Model
{
    protected $primaryKey = 'article_id';

    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'content',
        'summary',
        'featured_image_url',
        'status',
        'scheduled_at',
        'published_at',
        'like_count',
        'comment_count',
        'view_count',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When an article is being deleted, also delete its related data
        static::deleting(function ($article) {
            // Delete all comments associated with this article
            $article->comments()->delete();
            
            // Delete all likes associated with this article
            $article->likes()->delete();
            
            // Delete all saved articles associated with this article
            $article->savedArticles()->delete();
        });
    }

    /** articles relations with other tables **/
    //getting the author of the article "relation one to many an article belongs to a userprofile"
    public function author()
    {
        return $this->belongsTo(UserProfiles::class, 'author_id', 'profile_id');
    }

    // CORRECT relationship - Article has many ArticleLikes
    public function likes()
    {
        return $this->hasMany(ArticleLike::class, 'article_id', 'article_id');
    }

    // getting the comments of the article "relation one to many"
    public function comments()
    {
        return $this->hasMany(Comment::class, 'article_id', 'article_id');
    }

    // Add relationship for saved articles
    public function savedArticles()
    {
        return $this->hasMany(\App\Models\userSavedArticle::class, 'article_id', 'article_id');
    }

    /**
     * Get the author name attribute
     */
    public function getAuthorNameAttribute()
    {
        return $this->author->user->name ?? 'Unknown Author';
    }

    // getting the category of the article "relation one to one"
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'category_id', 'category_id');
    }
}
