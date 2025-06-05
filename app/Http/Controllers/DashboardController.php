<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\UserProfiles;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\ArticlesController;
class DashboardController extends Controller
{
    //get the current user profile id
    public function getCorrentUser():int
    {
        $user = Auth::id();
        $profile_id = UserProfiles::where('user_id', $user)->first()->profile_id;
        return $profile_id;
    }

    public function index()
    {
        Gate::authorize('accessDashboard', Auth::user());
        $profile_id = $this->getCorrentUser();

        $articles = $this->TopArticles(5, $profile_id);

        $user = $this->user();
        return view('dashboard.home', [
            'user' => $user,
            'articles' => $articles,
        ]); 
    }

    public function articles(Request $request)
    {
        Gate::authorize('accessDashboard', Auth::user());
        $profile_id = $this->getCorrentUser();

        $published = $this->publishedArticles($profile_id);
        $draft = $this->draftArticles($profile_id);
        // $scheduled = $this->scheduledArticles($profile_id);
        return view('dashboard.articles', [
            'published' => $published,
            'draft' => $draft,
            // 'scheduled' => $scheduled,
        ]);

    } 

    // get the user information 
    public function user()
    {
        //get teh authirised user
        $user = Auth::id();  
        $profile_id = UserProfiles::where('user_id', $user)->first()->profile_id;

        $user=UserProfiles::Select('*')
            ->with('user')
            ->where('profile_id', $profile_id)
            ->first();
        
        $totalArticles = Article::where('author_id', $profile_id)->count();
        // $totalFollwers = $user->followers()->count();
        $totalViews = Article::where('author_id', $profile_id)->sum('view_count');
        $totalLikes = Article::where('author_id', $profile_id)->sum('like_count');
        $totalcomment = Article::where('author_id', $profile_id)->sum('comment_count');

        return [
        'totalArticles' => $totalArticles,
        'totalViews' => $totalViews,
        'totalLikes' => $totalLikes,
        'totalComments' => $totalcomment,
        'userName' => $user->user->name ?? 'User',
        'userEmail' => $user->user->email ?? '',
        ];
    }
    //gitting the latest article by the user
    public function TopArticles($limit = 10, $user_id)
    {   
        if (!Gate::allows('accessDashboard') || is_null($user_id)) {
            abort(403, 'Unauthorized action.');
        }

        return Article::select('article_id','author_id', 'title', 'status','view_count' ,'created_at')
            ->with(['author.user', 'categorie'])
            ->where('author_id', $user_id)
            ->orderByRaw('(view_count + like_count + comment_count) DESC')
            ->take($limit)
            ->get();
    }

    //get the published articles
    public function publishedArticles($user_id,$limit = 10, Request $request = null)
    {   
        if (!Gate::allows('accessDashboard') || is_null($user_id)) {
            abort(403, 'Unauthorized action.');
        }

        //get the articles by the user
        $articles = Article::with(['author.user', 'categorie'])
            ->where('author_id', $user_id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
        
        // Append query parameters if request is provided
        if ($request) {
            $articles->appends($request->query());
        }
        
        return $articles;
        // dd($articles);
    }

    
    //get the draft articles
    public function draftArticles($user_id,$limit = 10,Request $request = null)
    {   
        if (!Gate::allows('accessDashboard') || is_null($user_id)) {
            abort(403, 'Unauthorized action.');
        }

        $articles = Article::select('article_id', 'title', 'status','view_count' ,'created_at')
            ->with(['author.user', 'categorie'])
            ->where('author_id', $user_id)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
            
        // Append query parameters if request is provided
        if ($request) {
            $articles->appends($request->query());
        }
        
        return $articles;
    }

    // //get the archived articles
    // public function scheduledArticles($user_id, $limit = 10)
    // {   
    //     if (!Gate::allows('accessDashboard') || is_null($user_id)) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     return Article::select('article_id', 'title', 'status','view_count' ,'created_at', 'scheduled_at')
    //         ->with(['author.user', 'categorie'])
    //         ->where('author_id', $user_id)
    //         ->where('status', 'scheduled')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate($limit);
            
    // }
}