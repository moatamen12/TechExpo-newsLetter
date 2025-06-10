<?php

namespace App\Services;

use App\Models\Subscriber;
use App\Models\userFollower;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SubscriberService
{
    /**
     * Sync followers as newsletter subscribers
     * Now they subscribe to the specific author they're following
     */
    public function syncFollowersAsSubscribers()
    {
        $followers = userFollower::with('follower', 'following')->get();
        
        foreach ($followers as $follow) {
            if ($follow->follower && $follow->follower->email) {
                try {
                    Subscriber::firstOrCreate([
                        'email' => $follow->follower->email,
                        'author_id' => $follow->following_id, // Subscribe to specific author
                    ], [
                        'user_id' => $follow->follower->user_id,
                        'subscription_type' => 'author'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create subscriber: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Subscribe a user when they follow someone
     * They subscribe to that specific author's newsletters
     */
    public function subscribeOnFollow($followerUserId)
    {
        $user = User::find($followerUserId);
        
        if ($user && $user->email) {
            // Get the author they just followed
            $latestFollow = userFollower::where('follower_id', $followerUserId)
                                      ->latest('created_at')
                                      ->first();
            
            if ($latestFollow) {
                try {
                    Subscriber::firstOrCreate([
                        'email' => $user->email,
                        'author_id' => $latestFollow->following_id,
                    ], [
                        'user_id' => $user->user_id,
                        'subscription_type' => 'author'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to subscribe user on follow: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Get subscribers for a specific author
     */
    public function getSubscribersForAuthor($authorId)
    {
        return Subscriber::where('author_id', $authorId)
                        ->active()
                        ->get();
    }

    /**
     * Get all subscribers for general newsletter
     */
    public function getGeneralNewsletterSubscribers()
    {
        return Subscriber::where('subscription_type', 'general')
                        ->active()
                        ->get();
    }
}