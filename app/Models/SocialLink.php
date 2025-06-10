<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'url',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Available social platforms
    public static $platforms = [
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'github' => 'GitHub',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'website' => 'Website'
    ];

    // Platform icons (Font Awesome classes)
    public static $platformIcons = [
        'facebook' => 'fab fa-facebook',
        'twitter' => 'fab fa-twitter',
        'instagram' => 'fab fa-instagram',
        'linkedin' => 'fab fa-linkedin',
        'github' => 'fab fa-github',
        'youtube' => 'fab fa-youtube',
        'tiktok' => 'fab fa-tiktok',
        'website' => 'fas fa-globe'
    ];

    // Platform colors
    public static $platformColors = [
        'facebook' => '#1877F2',
        'twitter' => '#1DA1F2',
        'instagram' => '#E4405F',
        'linkedin' => '#0A66C2',
        'github' => '#181717',
        'youtube' => '#FF0000',
        'tiktok' => '#000000',
        'website' => '#6B7280'
    ];

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get platform display name
     */
    public function getPlatformNameAttribute()
    {
        return self::$platforms[$this->platform] ?? ucfirst($this->platform);
    }

    /**
     * Get platform icon
     */
    public function getPlatformIconAttribute()
    {
        return self::$platformIcons[$this->platform] ?? 'fas fa-link';
    }

    /**
     * Get platform color
     */
    public function getPlatformColorAttribute()
    {
        return self::$platformColors[$this->platform] ?? '#6B7280';
    }

    /**
     * Scope for active links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
