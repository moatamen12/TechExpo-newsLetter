<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserProfiles;

class Subscriber extends Model
{
    protected $table = 'subscribers';

    protected $fillable = [
        'email',
        'user_id',   // refrenc the user taple
        'author_id', // refrenc the userProfiles table
        'subscription_type',
        'status',
        'subscribed_at',
        'unsubscribed_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // Set default values
    protected $attributes = [
        'subscription_type' => 'general',
        'status' => 'active'
    ];

    /**
     * Get the user associated with this subscription (if any)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
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
        return $query->where('status', 'active');
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
