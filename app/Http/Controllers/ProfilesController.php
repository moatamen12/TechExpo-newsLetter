<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userSavedArticle;
use App\Models\User;
use App\Models\userFollower;
use Illuminate\Support\Facades\Auth;


class ProfilesController extends Controller
{

    public function index()
    {
        // fetch the loged in user info
        $user = Auth::id();

        //get the profile info
        $user_info = User::where('user_id', $user)
            ->firstOrFail();

        // get the saved articles for the user
        $savedArticles = userSavedArticle::getSavedArticles($user)->get();

        // Get the writers that this user follows
        $followedWriters = userFollower::getFollowedWriters($user);

        return view('profile.profile',
        [   
            'activeTab' => 'info',
            'user' => $user_info,
            'savedArticles' => $savedArticles,
            'followedWriters' => $followedWriters,
        ]);
    }
}