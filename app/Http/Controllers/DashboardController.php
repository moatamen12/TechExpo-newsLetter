<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use App\Models\Categorie;
use App\Models\UserProfiles;
use App\Models\ArticleLike;
use App\Models\Comment;
use App\Models\userSavedArticle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $profile_id = $this->getCorrentUser();
        $user = $this->user();
        
        // Get stats data (moved from StatsController)
        $monthlyData = $this->getMonthlyPerformance($profile_id);
        $audienceGrowth = $this->getAudienceGrowth();
        $topArticles = $this->getTopPerformingArticles($profile_id);
        $categoryStats = $this->getCategoryStats($profile_id);
        $recentActivity = $this->getRecentActivity($profile_id);
        
        return view('dashboard.home', [
            'user' => $user,
            'monthlyData' => $monthlyData,
            'audienceGrowth' => $audienceGrowth,
            'topArticles' => $topArticles,
            'categoryStats' => $categoryStats,
            'recentActivity' => $recentActivity,
        ]);     
    }

    public function articles(Request $request)
    {
        $profile_id = $this->getCorrentUser();

        $published = $this->publishedArticles($profile_id);
        $draft = $this->draftArticles($profile_id);
        return view('dashboard.articles.articles', [
            'published' => $published,
            'draft' => $draft,
        ]);
    } 

    // get the user information 
    public function user()
    {
        $user = Auth::id();  
        $profile_id = UserProfiles::where('user_id', $user)->first()->profile_id;

        $user=UserProfiles::Select('*')
            ->with('user')
            ->where('profile_id', $profile_id)
            ->first();
        
        $totalArticles = Article::where('author_id', $profile_id)->count();
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

    // Stats methods (moved from StatsController)
    private function getMonthlyPerformance($authorId)
    {
        $months = [];
        $views = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;
            
            $monthlyViews = Article::where('author_id', $authorId)
                                 ->whereMonth('created_at', $date->month)
                                 ->whereYear('created_at', $date->year)
                                 ->where('status', 'published')
                                 ->sum('view_count');
            
            $views[] = $monthlyViews;
        }
        
        return [
            'months' => $months,
            'views' => $views
        ];
    }
    
    private function getAudienceGrowth()
    {
        $months = [];
        $users = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;
            
            $userCount = User::where('created_at', '<=', $date->endOfMonth())
                           ->count();
            $users[] = $userCount;
        }
        
        return [
            'months' => $months,
            'users' => $users
        ];
    }
    
    private function getTopPerformingArticles($authorId)
    {
        return Article::with(['author.user', 'categorie'])
                     ->where('author_id', $authorId)
                     ->where('status', 'published')
                     ->orderBy('view_count', 'desc')
                     ->take(5)
                     ->get()
                     ->map(function($article) {
                         return [
                             'article_id' => $article->article_id,
                             'title' => $article->title,
                             'author' => $article->author->user->name ?? 'Unknown',
                             'date' => $article->created_at->format('M j, Y'),
                             'views' => $article->view_count ?? 0,
                             'likes' => $article->like_count ?? 0,
                             'comments' => $article->comment_count ?? 0
                         ];
                     });
    }
    
    private function getCategoryStats($authorId)
    {
        $categories = Categorie::whereHas('articles', function($query) use ($authorId) {
                                $query->where('author_id', $authorId)
                                      ->where('status', 'published');
                            })
                            ->with(['articles' => function($query) use ($authorId) {
                                $query->where('author_id', $authorId)
                                      ->where('status', 'published');
                            }])
                            ->get()
                            ->map(function($category) use ($authorId) {
                                $count = $category->articles()
                                                 ->where('author_id', $authorId)
                                                 ->where('status', 'published')
                                                 ->count();
                                return [
                                    'name' => $category->name,
                                    'count' => $count,
                                    'percentage' => 0
                                ];
                            })
                            ->filter(function($item) {
                                return $item['count'] > 0;
                            });
        
        $total = $categories->sum('count');
        
        if ($total > 0) {
            $categories = $categories->map(function($item) use ($total) {
                $item['percentage'] = round(($item['count'] / $total) * 100);
                return $item;
            });
        }
        
        return $categories;
    }

    //gitting the latest article by the user
    public function TopArticles($limit = 10, $user_id)
    {   
        return Article::select('article_id','author_id', 'title', 'status','view_count' ,'created_at','category_id')
            ->with(['author.user', 'categorie'])
            ->where('author_id', $user_id)
            ->orderByRaw('(view_count + like_count + comment_count) DESC')
            ->take($limit)
            ->get();
    }

    //get the published articles
    public function publishedArticles($user_id,$limit = 10, Request $request = null)
    {   
        $articles = Article::with(['author.user', 'categorie'])
            ->where('author_id', $user_id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
        
        if ($request) {
            $articles->appends($request->query());
        }
        
        return $articles;
    }

    //get the draft articles
    public function draftArticles($user_id,$limit = 10,Request $request = null)
    {   
        $articles = Article::select('article_id', 'title', 'status','view_count' ,'category_id','created_at')
            ->with(['author.user', 'categorie'])
            ->where('author_id', $user_id)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
            
        if ($request) {
            $articles->appends($request->query());
        }
        
        return $articles;
    }

    private function getRecentActivity($authorId)
    {
        $activities = collect();
        
        // Get recent articles
        $recentArticles = Article::where('author_id', $authorId)
                                ->where('status', 'published')
                                ->orderBy('created_at', 'desc')
                                ->take(3)
                                ->get()
                                ->map(function($article) {
                                    return [
                                        'type' => 'article',
                                        'title' => 'Published Article',
                                        'description' => $article->title,
                                        'time' => $article->created_at->diffForHumans(),
                                        'created_at' => $article->created_at
                                    ];
                                });
        
        $activities = $activities->merge($recentArticles);
        
        // Get recent likes on your articles using ArticleLike model
        $recentLikes = ArticleLike::with(['user', 'article'])
                        ->whereHas('article', function($query) use ($authorId) {
                            $query->where('author_id', $authorId);
                        })
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get()
                        ->map(function($like) {
                            return [
                                'type' => 'like',
                                'title' => 'New Like',
                                'description' => ($like->user->name ?? 'Someone') . ' liked "' . ($like->article->title ?? 'your article') . '"',
                                'time' => $like->created_at->diffForHumans(),
                                'created_at' => $like->created_at
                            ];
                        });
        
        $activities = $activities->merge($recentLikes);
        
        // Get recent comments on your articles using Comment model
        $recentComments = Comment::with(['user', 'article'])
                           ->whereHas('article', function($query) use ($authorId) {
                               $query->where('author_id', $authorId);
                           })
                           ->orderBy('created_at', 'desc')
                           ->take(3)
                           ->get()
                           ->map(function($comment) {
                               return [
                                   'type' => 'comment',
                                   'title' => 'New Comment',
                                   'description' => ($comment->user->name ?? 'Someone') . ' commented on "' . ($comment->article->title ?? 'your article') . '"',
                                   'time' => $comment->created_at->diffForHumans(),
                                   'created_at' => $comment->created_at
                               ];
                           });
        
        $activities = $activities->merge($recentComments);
        
        // Get recent saves of your articles using userSavedArticle model
        $recentSaves = userSavedArticle::with(['user', 'article'])
                        ->whereHas('article', function($query) use ($authorId) {
                            $query->where('author_id', $authorId);
                        })
                        ->orderBy('saved_at', 'desc')
                        ->take(3)
                        ->get()
                        ->map(function($save) {
                            return [
                                'type' => 'save',
                                'title' => 'Article Saved',
                                'description' => ($save->user->name ?? 'Someone') . ' saved "' . ($save->article->title ?? 'your article') . '"',
                                'time' => $save->saved_at->diffForHumans(),
                                'created_at' => $save->saved_at
                            ];
                        });
        
        $activities = $activities->merge($recentSaves);
        
        // Add some sample activities if no real data
        if ($activities->isEmpty()) {
            $activities->push([
                'type' => 'article',
                'title' => 'Getting Started',
                'description' => 'Welcome to your dashboard! Start by creating your first article.',
                'time' => 'Just now',
                'created_at' => now()
            ]);
        }
        
        // Sort by created_at and take latest 5
        return $activities->sortByDesc('created_at')->take(5)->values();
    }
}