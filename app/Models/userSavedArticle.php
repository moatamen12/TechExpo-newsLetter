<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class userSavedArticle extends Model
{


    protected $table = 'user_saved_articles'; // Explicitly define if not following convention

    // If your primary key is not 'id' or not auto-incrementing, define it
    // protected $primaryKey = 'your_primary_key_name';
    // public $incrementing = false; 
    // protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'article_id',
        'saved_at', // Add saved_at here if you want to use mass assignment for it
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'saved_at' => 'datetime', // This will cast 'saved_at' to a Carbon instance
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

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
