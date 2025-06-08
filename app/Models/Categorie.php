<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    
    protected $fillable = [
        'name',
        'description'
    ];

    // Relationship with articles
    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id', 'category_id');
    }
}
