<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserProfiles;

class Newsletter extends Model
{
    use HasFactory;

    protected $table = 'newsletter';
    protected $primaryKey = 'id';

    protected $fillable = [
        'author_id',
        'title', 
        'content',
        'summary',
        'newsletter_type',
        'category_id',
        'featured_image',
        'status',
        'recipient_type',
        'selected_subscribers',
        'scheduled_at',
        'sent_at',
        'total_sent',
        'total_failed'
    ];

    protected $casts = [
        'selected_subscribers' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    public function author()
    {
        return $this->belongsTo(UserProfiles::class, 'author_id', 'profile_id');
    }

    public function category()
    {
        return $this->belongsTo(Categorie::class, 'category_id', 'category_id');
    }

    // Add these missing columns to your database migration
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }
}