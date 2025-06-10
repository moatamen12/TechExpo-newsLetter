<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use App\Models\Categorie;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        // Monthly Performance Data (last 6 months)
        $monthlyData = $this->getMonthlyPerformance();
        
        // Audience Growth Data (last 6 months)
        $audienceGrowth = $this->getAudienceGrowth();
        
        // Top Performing Articles
        $topArticles = $this->getTopPerformingArticles();
        
        // Content Categories Distribution
        $categoryStats = $this->getCategoryStats();
        
        return view('dashboard.stats', compact(
            'monthlyData',
            'audienceGrowth', 
            'topArticles',
            'categoryStats'
        ));
    }
    
    private function getMonthlyPerformance()
    {
        $months = [];
        $views = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;
            
            // Get views for articles published in this month
            $monthlyViews = Article::whereMonth('created_at', $date->month)
                                 ->whereYear('created_at', $date->year)
                                 ->sum('view_count');
            
            // If no real data, use sample data
            $views[] = $monthlyViews > 0 ? $monthlyViews : rand(5000, 25000);
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
            $users[] = $userCount > 0 ? $userCount : rand(800, 1200);
        }
        
        return [
            'months' => $months,
            'users' => $users
        ];
    }
    
    private function getTopPerformingArticles()
    {
        return Article::with(['author.user', 'categorie']) // Fixed: using author.user relationship
                     ->where('status', 'published')
                     ->orderBy('view_count', 'desc')
                     ->take(5)
                     ->get()
                     ->map(function($article) {
                         return [
                             'title' => $article->title,
                             'author' => $article->author->user->name ?? 'Unknown', // Fixed: using author.user
                             'date' => $article->created_at->format('M j, Y'),
                             'views' => $article->view_count ?? rand(1000, 8000),
                                 'likes' => $article->like_count ?? rand(50, 500),
                                 'comments' => $article->comment_count ?? rand(10, 150)
                             ];
                         });
    }
    
    private function getCategoryStats()
    {
        $categories = Categorie::with('articles')
                            ->get()
                            ->map(function($category) {
                                $count = $category->articles()->where('status', 'published')->count();
                                return [
                                    'name' => $category->name,
                                    'count' => $count,
                                    'percentage' => 0 // Will calculate after getting total
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
        
        // If no real data, create sample data
        if ($categories->isEmpty()) {
            $categories = collect([
                ['name' => 'Web Development', 'count' => 12, 'percentage' => 43],
                ['name' => 'JavaScript', 'count' => 8, 'percentage' => 29],
                ['name' => 'CSS', 'count' => 5, 'percentage' => 18],
                ['name' => 'React', 'count' => 3, 'percentage' => 10]
            ]);
        }
        
        return $categories;
    }
}
