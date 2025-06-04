<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    /** articles relations with outher tables **/
    //geting the auther of the article "relation one to many a an articles belongs to an userprofile"
    public function author()
    {
        return $this->belongsTo(UserProfiles::class, 'author_id', 'profile_id');
    }
    /**
     * Get the author's name
     * 
     * @return string
     */
    public function getAuthorNameAttribute()
    {
        return $this->author ? $this->author->name : 'Unknown Author';
    }

    // getting the category of the article "relation one to one"
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'category_id', 'category_id');
    }

    // getting the tags of the article "relation one to many"
    public function comments()
    {
        return $this->hasMany(Comment::class,'article_id','article_id');
    }
    /**
     * Get the catagorie's name
     * 
     * @return string
     */
    public function getCategorieNameAttribute()
    {
        return $this->categorie ? $this->categorie->name : 'Unknown Categorie';
    }


    /**
     * Get the comments 
     * 
     * @return string
    */
    public function getCommentCountAttribute()
    {
        return $this->comment ? $this->comment->count() : 0;
    }



    // trinding articles
    public function scopeTrendingArticles($query, $days = 7, $limit = 5){
        $date = Carbon::now()->subDays($days);
        
        return $query->with('author')
            ->where('status', 'published')
            ->where('created_at', '>=', $date)
            ->select('articles.*')

            ->selectRaw('(view_count * 1 + like_count * 2 + comment_count * 3) as trend_score')

            ->orderByDesc('trend_score')
            ->limit($limit);
    }

    // getting the latest articles
    public function scopeLatestArticles($query,$limit = 5){
        return $query->with('author')
                     ->where('status','published')
                     ->select('articles.*')
                     ->orderBy('created_at','desc')
                     ->take($limit);
    }


    // get the must views articles
    public function scopeMosViewsArticles($query,$limit = 5){
        return $query->with('author')
                     ->where('status','published')
                     ->orderBy('view_count','desc')
                     ->take($limit);
    }

    // get the articles by auther
    public function scopeArticleByAuthor($query,$autherID,$limit = 5){
        return $query->with('author')
                     ->where('status','published')
                     ->where('author_id',$autherID)
                     ->orderBy('created_at','desc')
                     ->take($limit);
    }

    // get the articles by category
    public function scopeArticleByCategory($query,$categoryID,$limit = 5){
        return $query->with('categorie')
                     ->with('author')
                     ->where('status','published')
                     ->where('category_id',$categoryID)
                     ->orderBy('created_at','desc')
                     ->take($limit);
    }



}
