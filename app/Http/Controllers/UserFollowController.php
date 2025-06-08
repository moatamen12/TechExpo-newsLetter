<?php

namespace App\Http\Controllers;

use App\Models\userFollower;
use App\Models\UserProfiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFollowController extends Controller
{
    /**
     * Toggle follow status for a user
     * 
     * @param Request $request
     * @param int $profileId The profile ID to follow/unfollow
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFollow(Request $request, $profileId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'You must be logged in'], 401);
        }

        try {
            $currentUserId = Auth::id();
            $profile = UserProfiles::findOrFail($profileId);
            
            // Check if trying to follow yourself
            if ($profile->user_id == $currentUserId) {
                return response()->json(['error' => 'You cannot follow yourself'], 400);
            }
            
            $existingFollow = userFollower::where('follower_id', $currentUserId)
                ->where('following_id', $profileId)
                ->first();
                
            if ($existingFollow) {
                // Unfollow
                $existingFollow->delete();
                
                // Update follower count
                $profile->decrement('followers_count');
                
                return response()->json([
                    'followed' => false,
                    'message' => 'Unfollowed successfully'
                ]);
            } else {
                // Follow
                userFollower::create([
                    'follower_id' => $currentUserId,
                    'following_id' => $profileId
                ]);
                
                // Update follower count
                $profile->increment('followers_count');
                
                return response()->json([
                    'followed' => true,
                    'message' => 'Followed successfully'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}