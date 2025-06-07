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
        //git the authenticated user
        $user = Auth::user();

        if($user->userProfile()->exists()){
            // // dd($user->userProfile()->exists());  
            // return view('author_profile', [
            //     'user' => $user,
            //     // 'savedArticles' => $user->savedArticles()->paginate(5),
            //     // 'followers' => userFollower::where('following_id', $user->id)->count(),
            //     // 'following' => userFollower::where('follower_id', $user->id)->count(),
            // ]);
        }
        elseif($user->exists()){
            return  $this->readerProfile($user);

        }
    }
    
    //reader profile
    public function readerProfile($user)
    {
        $savedArticles = userSavedArticle::with(['article', 'user'])
            ->where('user_id', $user->user_id)
            ->paginate(5);

        dd($savedArticles);
        return view('profile.reader_profile', [                
                'user_name' => $user->name,
                'user_email' => $user->email,
            'savedArticles' => $savedArticles,
            'followers' => userFollower::where('following_id', $user->user_id)->count(),
            // 'following' => userFollower::where('follower_id', $user->id)->count(),
        ]);
    }
}