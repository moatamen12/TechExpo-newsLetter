<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserProfiles;

class Newsletter extends Model
{
    use HasFactory;

    protected $table = 'newsletter';
    
    protected $fillable = [
        'title',
        'content',
        'description',
        'featured_image',
        'status',
        'author_id',
        'recipient_type',
        'selected_subscribers',
        'total_sent',
        'total_failed',
        'sent_at',
        'scheduled_at'
    ];

    protected $casts = [
        'selected_subscribers' => 'array',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(UserProfiles::class, 'author_id', 'profile_id');
    }
}