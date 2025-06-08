<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userSavedArticle;
use App\Models\User;
use App\Models\Article;
use App\Models\userFollower; // Corrected model name
use App\Models\UserProfiles;
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
    public function readerProfile(User $user) 
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

        $followedWritersModels = userFollower::getFollowedWriters($user->user_id)->get();
        // dd($followedWritersModels->profile_id);
        
            // dd($followedWritersModels->first()->following_id);
        
        return view('profile.reader_profile', [                
            'user_name' => $user->name,
            'user_email' => $user->email,
            'savedArticles' => $savedUserArticles, 
            'followedWriters' => $followedWritersModels, // Pass followed writers to the view
            'followers' => userFollower::where('following_id', $user->user_id)->count(),
            // 'following' => userFollower::where('follower_id', $user->user_id)->count(), // Corrected to user_id
        ]);
    }


    public function show($profile_id){

        $authorProfile = UserProfiles::where('profile_id', $profile_id)->first();

        if (!$authorProfile) {
            return redirect()->back()->with('error', 'Author profile not found.');
        }

        $authorUser = User::with('userProfile')
                           ->find($authorProfile->user_id); 
        // dd($authorUser);
        if (!$authorUser) {
            return redirect()->back()->with('error', 'User not found for this profile.');
        }
        
        $articles = Article::with(['author.user', 'categorie'])
            ->where('author_id', $authorProfile->profile_id)
            ->where('status', 'published')
            // Order by the sum of likes and comments in descending order
            ->orderByRaw('(IFNULL(like_count, 0) + IFNULL(comment_count, 0)) DESC')
            // Take the top 5 articles
            ->take(5)
            ->get();

        return view('profile.show', [
            'author' => $authorUser, 
            'articles' => $articles,
            'followers' => userFollower::where('following_id', $authorProfile->profile_id)->count(),

            // 'following' => userFollower::where('follower_id', $authorProfile->profile_id)->count(),
        ]);
    }


    // public function edit_reder(Request $request)
    // {   
        
    // }
}