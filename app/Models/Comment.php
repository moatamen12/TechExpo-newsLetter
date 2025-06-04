<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Article;

class Comment extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'comment_id';
    protected $fillable = [
        'user_id',
        'article_id',
        'parent_id',
        'content'
    ];
    
    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Define relationship with Article model
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }

    //the replay relation with the comment model
    public function replies(){
        return $this->hasMany(Comment::class, 'parent_id', 'comment_id');
    }
    
    //relation with the parent comment 
    public function parent(){
        return $this->belongsTo(Comment::class, 'parent_id', 'comment_id');
    }
}

