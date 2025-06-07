<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class userSavedArticle extends Model
{
    protected $table = 'user_saved_articles';
    protected $fillable = ['user_id', 'article_id'];
    
    // Fix the relationships - use 'id' for User model (standard Laravel)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    // Keep article relationship as is if your articles table uses article_id
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }

    public function scopeGetSavedArticles($query, $userId)
    {
        return $query->with(['user', 'article'])
            ->where('user_id', $userId)
            ->orderBy('saved_at', 'desc'); // Use created_at instead of saved_at
    }
    
}
