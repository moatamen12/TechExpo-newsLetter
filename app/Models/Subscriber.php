<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserProfiles;

class Subscriber extends Model
{
    use HasFactory;

    protected $table = 'subscribers';

    protected $fillable = [
        'name',
        'email',
        'author_id',
        'user_id',
        'is_active',
        'last_activity_at',
        'subscription_type',
        'subscribed_at',
        'unsubscribed_at',
        'status'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with this subscription (if any)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the author this subscription is for (if any)
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(UserProfiles::class, 'author_id', 'profile_id');
    }

    /**
     * Scope for active subscribers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for subscribers by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for subscribers of a specific author
     */
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Scope for general newsletter subscribers
     */
    public function scopeGeneralNewsletter($query)
    {
        return $query->where('subscription_type', 'general');
    }

    /**
     * Scope for author-specific subscribers
     */
    public function scopeAuthorSubscriptions($query)
    {
        return $query->where('subscription_type', 'author');
    }
}
