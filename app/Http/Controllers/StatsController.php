<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use App\Models\Categorie;
use App\Models\UserProfiles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        // Get the authenticated user's profile
        $user = Auth::user();
        $userProfile = UserProfiles::where('user_id', $user->user_id)->first();
        
        if (!$userProfile) {
            return redirect()->route('dashboard')->with('error', 'Author profile not found.');
        }
        
        $authorId = $userProfile->profile_id;
        
        // Monthly Performance Data (last 6 months) - filtered by author
        $monthlyData = $this->getMonthlyPerformance($authorId);
        
        // Audience Growth Data (last 6 months) - this might still be global or author-specific
        $audienceGrowth = $this->getAudienceGrowth();
        
        // Top Performing Articles - filtered by author
        $topArticles = $this->getTopPerformingArticles($authorId);
        
        // Content Categories Distribution - filtered by author
        $categoryStats = $this->getCategoryStats($authorId);
        
        return view('dashboard.stats', compact(
            'monthlyData',
            'audienceGrowth', 
            'topArticles',
            'categoryStats'
        ));
    }
    
    private function getMonthlyPerformance($authorId)
    {
        $months = [];
        $views = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;
            
            // Get views for articles published by this author in this month
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
            
            // Get cumulative user count up to this month
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
        $TopPerformingArticles = Article::with(['author.user', 'categorie'])
                     ->where('author_id', $authorId)
                     ->where('status', 'published')
                     ->orderBy('view_count', 'desc')
                     ->take(5)
                     ->get()
                     ->map(function($article) {
                         return [
                             'article_id' => $article->article_id, // Add this line
                             'title' => $article->title,
                             'author' => $article->author->user->name ?? 'Unknown',
                             'date' => $article->created_at->format('M j, Y'),
                             'views' => $article->view_count ?? 0,
                             'likes' => $article->like_count ?? 0,
                             'comments' => $article->comment_count ?? 0
                         ];
                     });
        // dd($TopPerformingArticles);
        return $TopPerformingArticles;
                     
    }
    
    private function getCategoryStats($authorId)
    {
        // Get categories that have articles by this author
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
}
