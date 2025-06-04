<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class userSavedArticle extends Model
{
    //relation with the user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    //relation with the article
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }

    public function scopegetSavedArticles($query,$user)
    {
        return $query->select('*')
            ->with([
                'user' => function($query) {
                    $query->select('user_id', 'name')->where('status', 'published');
                },
                'article' => function($query) {
                    $query->select('article_id', 'title');
                }
            ])
            ->where('user_id', $user)
            ->orderBy('saved_at', 'desc');   
    }
    
}
