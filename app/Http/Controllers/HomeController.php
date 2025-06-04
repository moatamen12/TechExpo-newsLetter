<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    // for the index page

    /**
     * Show the home page.
     *
     * @return \Illuminate\View\View
    */
    public function index()
    { 
        $articlesPerPage = 9;

        $articles = Article::with('author', 'categorie')
                            ->select('article_id', 'author_id', 'category_id', 'title', 'summary', 'featured_image_url', 'status',  'published_at')
                            ->where('status', 'published') 
                            ->latest()
                            ->paginate($articlesPerPage);
        

        $featuredArticle = Article::with('author', 'categorie')
                            ->select('article_id', 'author_id', 'category_id', 'title', 'summary', 'featured_image_url', 'status',  'published_at')
                            ->where('status', 'published')
                            ->latest()
                            ->first();

        return view('home.index', compact('articles', 'featuredArticle'));
    }

    
    public function homePartials()
    {
        return view('home.partials'); 
    }

    public function loadMoreArticles(Request $request)
    {
        $articlesPerPage = 9; 
        $page = $request->get('page', 2); 

        $articles = Article::with('author.user', 'categorie')
                            ->select('article_id', 'author_id', 'category_id', 'title', 'summary', 'featured_image_url', 'status', 'published_at')
                            ->where('status', 'published')
                            ->latest()
                            ->skip(($page - 1) * $articlesPerPage)
                            ->take($articlesPerPage)
                            ->get();

        $html = '';
        if ($request->ajax()) {
            if ($articles->count() > 0) {
                foreach ($articles as $article) {
                    $html .= view('components.card_vertical', compact('article'))->render();
                }
            }
            
            // Check if there are more articles 
            $totalArticles = Article::where('status', 'published')->count();
            $hasMorePages = ($page * $articlesPerPage) < $totalArticles;

            return response()->json([
                'html' => $html, 
                'hasMorePages' => $hasMorePages,
                'nextPage' => $page + 1,
                'debug' => [
                    'current_page' => $page,
                    'articles_loaded' => $articles->count(),
                    'total_articles' => $totalArticles,
                    'skip' => ($page - 1) * $articlesPerPage,
                    'take' => $articlesPerPage
                ]
            ]);
        }

        return redirect()->route('home');
    }
}