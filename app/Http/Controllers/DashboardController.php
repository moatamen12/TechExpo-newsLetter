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
use App\Models\userFollower;
use App\Models\Newsletter; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $user = Auth::user();
        $profile_id = $this->getCorrentUser();
        
        // Get user dashboard data with real values only
        $userData = $this->user();
        
        // Get real stats data
        $monthlyData = $this->getMonthlyPerformance($profile_id);
        $audienceGrowth = $this->getAudienceGrowthData($user);
        $topArticles = $this->getTopPerformingArticles($profile_id);
        $categoryStats = $this->getCategoryStats($profile_id);
        $recentActivity = $this->getRecentActivity($profile_id);
        
        return view('dashboard.home', compact(
            'userData',
            'monthlyData',
            'audienceGrowth',
            'topArticles',
            'categoryStats',
            'recentActivity'
        ));     
    }

    public function articles(Request $request)
    {
        $profile_id = $this->getCorrentUser();

        $published = $this->publishedArticles($profile_id, 10, $request);
        $draft = $this->draftArticles($profile_id, 10, $request);
        
        $allArticles = Article::with(['author.user', 'categorie'])
            ->where('author_id', $profile_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        if ($request) {
            $allArticles->appends($request->query());
        }
        
        return view('dashboard.articles.articles', [
            'published' => $published,
            'draft' => $draft,
            'allArticles' => $allArticles,
        ]);
    } 

    // get the user information with only real data
    public function user()
    {
        $user = Auth::id();  
        $profile_id = UserProfiles::where('user_id', $user)->first()->profile_id;

        $userProfile = UserProfiles::with('user')
            ->where('profile_id', $profile_id)
            ->first();
    
        // Get real data only - no fallbacks to 0
        $totalArticles = Article::where('author_id', $profile_id)
            ->where('status', 'published')
            ->count();
            
        $totalViews = Article::where('author_id', $profile_id)
            ->where('status', 'published')
            ->sum('view_count') ?: 0;
            
        $totalLikes = Article::where('author_id', $profile_id)
            ->where('status', 'published')
            ->sum('like_count') ?: 0;
            
        $totalComments = Article::where('author_id', $profile_id)
            ->where('status', 'published')
            ->sum('comment_count') ?: 0;

        // Get actual follower count
        $followersCount = userFollower::where('following_id', $profile_id)->count();

        // Get percentage changes with real calculations
        $articlesChange = $this->getArticlePercentageChange($profile_id);
        $viewsChange = $this->getViewsPercentageChange($profile_id);
        $followersChange = $this->getFollowersPercentageChange($profile_id);
        $reactionsChange = $this->getReactionsPercentageChange($profile_id);

        return [
            'totalArticles' => $totalArticles,
            'totalViews' => $totalViews,
            'totalLikes' => $totalLikes,
            'totalComments' => $totalComments,
            'followersCount' => $followersCount,
            'userName' => $userProfile->user->name ?? 'User',
            'userEmail' => $userProfile->user->email ?? '',
            'socialTwitter' => $userProfile->social_twitter,
            'socialLinkedin' => $userProfile->social_linkedin,
            'socialGithub' => $userProfile->social_github,
            'socialWebsite' => $userProfile->social_website,
            'profilePhoto' => $userProfile->profile_photo,
            'bio' => $userProfile->bio,
            'title' => $userProfile->title,
            // Real percentage data
            'articlesPercentage' => $articlesChange['percentage'],
            'articlesDirection' => $articlesChange['direction'],
            'viewsPercentage' => $viewsChange['percentage'],
            'viewsDirection' => $viewsChange['direction'],
            'followersPercentage' => $followersChange['percentage'],
            'followersDirection' => $followersChange['direction'],
            'reactionsPercentage' => $reactionsChange['percentage'],
            'reactionsDirection' => $reactionsChange['direction'],
        ];
    }

    // Get monthly performance with real data only
    private function getMonthlyPerformance($authorId)
    {
        $months = [];
        $views = [];
        $articles = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;
            
            // Get actual monthly views from published articles
            $monthlyViews = Article::where('author_id', $authorId)
                                 ->whereMonth('created_at', $date->month)
                                 ->whereYear('created_at', $date->year)
                                 ->where('status', 'published')
                                 ->sum('view_count') ?: 0;
            
            // Get actual monthly articles published
            $monthlyArticles = Article::where('author_id', $authorId)
                                     ->whereMonth('created_at', $date->month)
                                     ->whereYear('created_at', $date->year)
                                     ->where('status', 'published')
                                     ->count();
            
            $views[] = $monthlyViews;
            $articles[] = $monthlyArticles;
        }
        
        return [
            'months' => $months,
            'views' => $views,
            'articles' => $articles
        ];
    }
    
    // Get real audience growth data
    private function getAudienceGrowthData($user)
    {
        if (!$user->userProfile) {
            return [
                'months' => [],
                'followers' => []
            ];
        }

        $months = [];
        $followers = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;
            
            // Count actual followers up to the end of this month
            $followerCount = userFollower::where('following_id', $user->userProfile->profile_id)
                ->where('created_at', '<=', $date->endOfMonth())
                ->count();
            
            $followers[] = $followerCount;
        }
        
        return [
            'months' => $months,
            'followers' => $followers
        ];
    }
    
    // Get real top performing articles
    private function getTopPerformingArticles($authorId)
    {
        return Article::with(['author.user', 'categorie'])
                     ->where('author_id', $authorId)
                     ->where('status', 'published')
                     ->orderByRaw('(view_count + like_count + comment_count) DESC')
                     ->take(10)
                     ->get()
                     ->map(function($article) {
                         return [
                             'article_id' => $article->article_id,
                             'title' => $article->title,
                             'author' => $article->author->user->name ?? 'Unknown',
                             'date' => $article->created_at->format('M j, Y'),
                             'views' => $article->view_count ?: 0,
                             'likes' => $article->like_count ?: 0,
                             'comments' => $article->comment_count ?: 0
                         ];
                     });
    }
    
    // Get real category statistics
    private function getCategoryStats($authorId)
    {
        $categories = Categorie::whereHas('articles', function($query) use ($authorId) {
                                $query->where('author_id', $authorId)
                                      ->where('status', 'published');
                            })
                            ->withCount(['articles' => function($query) use ($authorId) {
                                $query->where('author_id', $authorId)
                                      ->where('status', 'published');
                            }])
                            ->having('articles_count', '>', 0)
                            ->get()
                            ->map(function($category) {
                                return [
                                    'name' => $category->name,
                                    'count' => $category->articles_count,
                                    'percentage' => 0
                                ];
                            });
        
        $total = $categories->sum('count');
        
        if ($total > 0) {
            $categories = $categories->map(function($item) use ($total) {
                $item['percentage'] = round(($item['count'] / $total) * 100, 1);
                return $item;
            });
        }
        
        return $categories->values();
    }

    //get real published articles
    public function publishedArticles($user_id, $limit = 10, Request $request = null)
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

    //get real draft articles
    public function draftArticles($user_id, $limit = 10, Request $request = null)
    {   
        $articles = Article::with(['author.user', 'categorie'])
            ->where('author_id', $user_id)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
            
        if ($request) {
            $articles->appends($request->query());
        }
        
        return $articles;
    }

    // Get real recent activity
    private function getRecentActivity($authorId)
    {
        $activities = collect();
        
        // Get recent articles - real data only (reduced to 2)
        $recentArticles = Article::where('author_id', $authorId)
                                ->where('status', 'published')
                                ->orderBy('created_at', 'desc')
                                ->take(2)
                                ->get()
                                ->map(function($article) {
                                    return [
                                        'type' => 'article',
                                        'title' => 'Published Article',
                                        'description' => $article->title,
                                        'time' => $article->created_at->diffForHumans(),
                                        'created_at' => $article->created_at,
                                        'icon' => 'fas fa-file-alt',
                                        'color' => 'text-success',
                                        'url' => route('articles.show', $article->article_id)
                                    ];
                                });
    
        $activities = $activities->merge($recentArticles);
        
        // Get comprehensive newsletter activities - real data only (reduced quantities)
        try {
            $recentNewsletterDrafts = Newsletter::where('author_id', $authorId)
                                    ->where('status', 'draft')
                                    ->orderBy('created_at', 'desc')
                                    ->take(1)
                                    ->get()
                                    ->map(function($newsletter) {
                                        return [
                                            'type' => 'newsletter_draft',
                                            'title' => 'Newsletter Draft Created',
                                            'description' => 'Created draft "' . $newsletter->title . '"',
                                            'time' => $newsletter->created_at->diffForHumans(),
                                            'created_at' => $newsletter->created_at,
                                            'icon' => 'fas fa-edit',
                                            'color' => 'text-secondary',
                                            'url' => route('newsletter.show', $newsletter->id),
                                            'status' => $newsletter->status
                                        ];
                                    });
            
            // Newsletter scheduling - reduced to 1
            $recentNewsletterScheduled = Newsletter::where('author_id', $authorId)
                                    ->where('status', 'scheduled')
                                    ->orderBy('scheduled_at', 'desc')
                                    ->take(1)
                                    ->get()
                                    ->map(function($newsletter) {
                                        return [
                                            'type' => 'newsletter_scheduled',
                                            'title' => 'Newsletter Scheduled',
                                            'description' => 'Scheduled "' . $newsletter->title . '" for ' . $newsletter->scheduled_at->format('M j, Y g:i A'),
                                            'time' => $newsletter->updated_at->diffForHumans(),
                                            'created_at' => $newsletter->updated_at,
                                            'icon' => 'fas fa-clock',
                                            'color' => 'text-warning',
                                            'url' => route('newsletter.show', $newsletter->id),
                                            'status' => $newsletter->status,
                                            'scheduled_for' => $newsletter->scheduled_at->format('M j, Y g:i A')
                                        ];
                                    });
            
            // Newsletter sending - reduced to 2
            $recentNewslettersSent = Newsletter::where('author_id', $authorId)
                                ->where('status', 'sent')
                                ->orderBy('sent_at', 'desc')
                                ->take(2)
                                ->get()
                                ->map(function($newsletter) {
                                    $successCount = $newsletter->success_count ?? 0;
                                    $failedCount = $newsletter->failed_count ?? 0;
                                    $totalCount = $newsletter->recipients_count ?? ($successCount + $failedCount);
                                    
                                    $description = 'Sent "' . $newsletter->title . '" to ' . $totalCount . ' subscriber(s)';
                                    if ($successCount > 0) {
                                        $description .= ' (' . $successCount . ' successful';
                                        if ($failedCount > 0) {
                                            $description .= ', ' . $failedCount . ' failed';
                                        }
                                        $description .= ')';
                                    }
                                    
                                    return [
                                        'type' => 'newsletter_sent',
                                        'title' => 'Newsletter Sent',
                                        'description' => $description,
                                        'time' => $newsletter->sent_at->diffForHumans(),
                                        'created_at' => $newsletter->sent_at,
                                        'icon' => 'fas fa-paper-plane',
                                        'color' => 'text-success',
                                        'url' => route('newsletter.show', $newsletter->id),
                                        'status' => $newsletter->status,
                                        'stats' => [
                                            'total' => $totalCount,
                                            'success' => $successCount,
                                            'failed' => $failedCount
                                        ]
                                    ];
                                });
            
            // Newsletter failures - reduced to 1
            $recentNewslettersFailed = Newsletter::where('author_id', $authorId)
                                    ->where('status', 'failed')
                                    ->orderBy('updated_at', 'desc')
                                    ->take(1)
                                    ->get()
                                    ->map(function($newsletter) {
                                        return [
                                            'type' => 'newsletter_failed',
                                            'title' => 'Newsletter Send Failed',
                                            'description' => 'Failed to send "' . $newsletter->title . '"',
                                            'time' => $newsletter->updated_at->diffForHumans(),
                                            'created_at' => $newsletter->updated_at,
                                            'icon' => 'fas fa-exclamation-triangle',
                                            'color' => 'text-danger',
                                            'url' => route('newsletter.show', $newsletter->id),
                                            'status' => $newsletter->status
                                        ];
                                    });
            
            // Merge all newsletter activities
            $activities = $activities->merge($recentNewsletterDrafts);
            $activities = $activities->merge($recentNewsletterScheduled);
            $activities = $activities->merge($recentNewslettersSent);
            $activities = $activities->merge($recentNewslettersFailed);
            
        } catch (\Exception $e) {
            Log::warning('Failed to load newsletter activities for dashboard', [
                'author_id' => $authorId,
                'error' => $e->getMessage()
            ]);
            // Continue without newsletter activities if there's an issue
        }
        
        // Get real followers - reduced to 1
        try {
            $recentFollowers = userFollower::with(['follower'])
                            ->where('following_id', $authorId)
                            ->orderBy('created_at', 'desc')
                            ->take(1)
                            ->get()
                            ->map(function($follow) {
                                return [
                                    'type' => 'follower',
                                    'title' => 'New Follower',
                                    'description' => ($follow->follower->name ?? 'Someone') . ' started following you',
                                    'time' => $follow->created_at->diffForHumans(),
                                    'created_at' => $follow->created_at,
                                    'icon' => 'fas fa-user-plus',
                                    'color' => 'text-info',
                                    'url' => route('dashboard.subscribers')
                                ];
                            });
            
            $activities = $activities->merge($recentFollowers);
        } catch (\Exception $e) {
            // Continue without followers if there's an issue
        }
        
        // Get real likes - reduced to 1
        try {
            $recentLikes = DB::table('article_likes')
                            ->join('articles', 'article_likes.article_id', '=', 'articles.article_id')
                            ->join('users', 'article_likes.user_id', '=', 'users.user_id')
                            ->where('articles.author_id', $authorId)
                            ->orderBy('article_likes.created_at', 'desc')
                            ->take(1)
                            ->select(
                                'users.name as user_name',
                                'articles.title as article_title',
                                'articles.article_id',
                                'article_likes.created_at'
                            )
                            ->get()
                            ->map(function($like) {
                                return [
                                    'type' => 'like',
                                    'title' => 'New Like',
                                    'description' => ($like->user_name ?? 'Someone') . ' liked "' . ($like->article_title ?? 'your article') . '"',
                                    'time' => Carbon::parse($like->created_at)->diffForHumans(),
                                    'created_at' => Carbon::parse($like->created_at),
                                    'icon' => 'fas fa-heart',
                                    'color' => 'text-danger',
                                    'url' => route('articles.show', $like->article_id)
                                ];
                            });
            
            $activities = $activities->merge($recentLikes);
        } catch (\Exception $e) {
            // Continue without likes
        }
        
        // Only show the last 5 activities - sorted by most recent
        return $activities->sortByDesc('created_at')->take(5)->values();
    }

    // Real percentage calculations
    private function getArticlePercentageChange($authorId)
    {
        $currentMonth = Article::where('author_id', $authorId)
                          ->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->where('status', 'published')
                          ->count();
    
        $lastMonth = Article::where('author_id', $authorId)
                       ->whereMonth('created_at', now()->subMonth()->month)
                       ->whereYear('created_at', now()->subMonth()->year)
                       ->where('status', 'published')
                       ->count();
    
        return $this->calculatePercentageChange($currentMonth, $lastMonth);
    }

    private function getViewsPercentageChange($authorId)
    {
        $currentMonth = Article::where('author_id', $authorId)
                          ->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->where('status', 'published')
                          ->sum('view_count') ?: 0;
    
        $lastMonth = Article::where('author_id', $authorId)
                       ->whereMonth('created_at', now()->subMonth()->month)
                       ->whereYear('created_at', now()->subMonth()->year)
                       ->where('status', 'published')
                       ->sum('view_count') ?: 0;
    
        return $this->calculatePercentageChange($currentMonth, $lastMonth);
    }

    private function getReactionsPercentageChange($authorId)
    {
        $currentMonthLikes = Article::where('author_id', $authorId)
                               ->whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->where('status', 'published')
                               ->sum('like_count') ?: 0;
    
        $currentMonthComments = Article::where('author_id', $authorId)
                                  ->whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->where('status', 'published')
                                  ->sum('comment_count') ?: 0;
    
        $lastMonthLikes = Article::where('author_id', $authorId)
                            ->whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year)
                            ->where('status', 'published')
                            ->sum('like_count') ?: 0;
    
        $lastMonthComments = Article::where('author_id', $authorId)
                               ->whereMonth('created_at', now()->subMonth()->month)
                               ->whereYear('created_at', now()->subMonth()->year)
                               ->where('status', 'published')
                               ->sum('comment_count') ?: 0;
    
        $currentTotal = $currentMonthLikes + $currentMonthComments;
        $lastTotal = $lastMonthLikes + $lastMonthComments;
    
        return $this->calculatePercentageChange($currentTotal, $lastTotal);
    }

    private function getFollowersPercentageChange($authorId)
    {
        $currentMonth = userFollower::where('following_id', $authorId)
                      ->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year)
                      ->count();

        $lastMonth = userFollower::where('following_id', $authorId)
                   ->whereMonth('created_at', now()->subMonth()->month)
                   ->whereYear('created_at', now()->subMonth()->year)
                   ->count();

        return $this->calculatePercentageChange($currentMonth, $lastMonth);
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            if ($current > 0) {
                return ['percentage' => 100, 'direction' => 'up'];
            }
            return ['percentage' => 0, 'direction' => 'neutral'];
        }
    
        $percentageChange = (($current - $previous) / $previous) * 100;
    
        return [
            'percentage' => abs($percentageChange),
            'direction' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral')
        ];
    }
}