<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userSavedArticle;
use App\Models\User;
use App\Models\userFollower; // Corrected model name
use Illuminate\Support\Facades\Auth;


class ProfilesController extends Controller
{

    public function index()
    {
        //get the authenticated user
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }
        // Simplified logic: always show readerProfile for now, 
        // you can add author-specific logic later if needed.
        return  $this->readerProfile($user);
    }
    
    //reader profile
    public function readerProfile(User $user) // It's good practice to type-hint the User model
    {
        $savedUserArticles = userSavedArticle::with([
            'article' => function ($query) {
                $query->with([
                    'author' => function ($subQuery) {
                        $subQuery->with('user'); // This loads the User model (author) via UserProfile
                    },
                    'categorie' // Article model has a 'categorie' relationship
                ]);
            }
        ])
        ->where('user_id', $user->user_id) 
        ->latest('saved_at') // Assuming 'saved_at' is the correct column in user_saved_articles
        ->paginate(5);

        // Fetch writers the user is following
        // The getFollowedWriters scope returns a collection of userFollower models,
        // where each 'followed' relationship is the User model of the writer.
        $followedWritersModels = userFollower::getFollowedWriters($user->user_id)->get();

        // For debugging, you can uncomment this to inspect the structure:
        // dd($savedUserArticles, $followedWritersModels); 

        return view('profile.reader_profile', [                
            'user_name' => $user->name,
            'user_email' => $user->email,
            'savedArticles' => $savedUserArticles, 
            'followedWriters' => $followedWritersModels, // Pass followed writers to the view
            'followers' => userFollower::where('following_id', $user->user_id)->count(),
            // 'following' => userFollower::where('follower_id', $user->user_id)->count(), // Corrected to user_id
        ]);
    }
}