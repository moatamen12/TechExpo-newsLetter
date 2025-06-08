<?php

namespace App\Http\Controllers;

use App\Models\UserProfiles;
use App\Models\userFollower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDO;

class ReaderEnteractions extends Controller
{
    //following a writer "userProfile"
    public function follow(UserProfiles $profile)
    {
        $user = Auth::user();

        if($user->user_id == $profile->user_id){
            return response()->json(['success' => false, 'message' => 'You cannot follow yourself.'], 422);
        }

        //if already following the user
        $isFollowing = userFollower::where('follower_id', $user->user_id)
                                    ->where('following_id', $profile->user_id)
                                    ->exists();
        
        if ($isFollowing) {
            return response()->json(['success' => false, 'message' => 'Already following this user.'], 422);
        }

        userFollower::create([
            'follower_id' => $user->user_id,
            'following_id' => $profile->profile_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Successfully followed.', 'is_following' => true]);
    }
    
    //unfollowing a writer "userProfile"
    public function unfollow(UserProfiles $profile)
    {
        $user = Auth::user();

        $follow = userFollower::where('follower_id', $user->user_id)
                               ->where('following_id', $profile->profile_id) // Use profile_id
                               ->first();

        if (!$follow) {
            return response()->json(['success' => false, 'message' => 'You are not following this user.'], 422);
        }

        $follow->delete();

        return response()->json(['success' => true, 'message' => 'Successfully unfollowed.', 'is_following' => false]);
    }
}
