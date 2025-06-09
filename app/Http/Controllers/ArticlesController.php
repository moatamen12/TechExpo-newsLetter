<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Comment;
use App\Models\ArticleLike;
use App\Models\UserProfiles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Import Carbon for date handling
use Illuminate\Support\Facades\Mail; // Import Mail facade
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;


class ArticlesController extends Controller
{
    // Display the list of articles
    public function index(Request $request){
        $query = Article::with(['author.user', 'categorie']) // Add 'categorie' here
                        ->where('status', 'published');

        // Apply search filter if provided
        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('content', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('summary', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('categorie', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('author.user', function($authorQuery) use ($searchTerm) {
                      $authorQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        // Apply category filter if provided
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $articles = $query->latest('published_at')->paginate(8);
        
        return view('articles.articles', compact('articles'));
    }
    
    // display the article by it's id
    public function show($article_id){
        $article = Article::select('*')
                    ->with([
                        'author.user' => function($query) {
                            $query->select('user_id', 'name');
                        },
                        'categorie' // Make sure this is loaded
                    ])
                    ->where('status', 'published')
                    ->where('article_id', $article_id)
                    ->firstOrFail();
        // dd($article->name);
        
        $relatedArticles = $this->relatedArticles($article->article_id, $article->author_id);

        // Check if this article has been viewed in the current session
        $viewedArticles = session()->get('viewed_articles', []);
        // $article->increment('view_count'); // Increment view count
        
        // Only increment if the user hasn't viewed this article in the current session
        if (!in_array($article_id, $viewedArticles)) {
            $article->increment('view_count');
            
            // Add this article to the session's viewed articles
            $viewedArticles[] = $article_id;
            session()->put('viewed_articles', $viewedArticles);
        }

        // Fetch parent comments (top-level comments only)
        $parentComments = Comment::where('article_id', $article_id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(10);
        
        return view('articles.show', [
            'article' => $article,
            'comments' => $parentComments,
            'relatedArticles' => $relatedArticles
        ]);
    }

    //create an article
    public function create(){
        //authinticate the user
        // Gate::authorize('accessDashboard', Auth::user());      
        $categories = \App\Models\Categorie::orderBy('name')->get(); ; // Fetch categories
        return view('dashboard.articles.create', compact('categories'));
        
    }

    //stor the article in the db
    public function store(Request $request){
        //authinticate the user
        // Gate::authorize('accessDashboard', Auth::user()); 

        //get the authenticated user
        $user = Auth::id();
        $profile = \App\Models\UserProfiles::where('user_id', $user)->first();

        if (!$profile) {
        return redirect()->route('subscribe')
            ->with('error', 'You need to be an Author To Posting Articles');
        }
    
        //validate the inputs
        $validator = $this->validating($request);

        if ($validator->fails()) {
            return redirect()->back() 
                ->withErrors($validator) 
                ->withInput(); 
        }

        // Get validated data if validation passes
        $validated = $validator->validated(); 

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() .$user .'_' . $image->getClientOriginalName();
            $imagePath = 'featured_images/' . $filename;
            $image->storeAs('featured_images', $filename,'public');
        }

        // Create the article
        $article = Article::create(
        [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'summary' => $validated['summary'] ?? substr(strip_tags($validated['content']), 0, 150),
            'featured_image_url' => $imagePath,
            'category_id' => $validated['category_id'], // ← Use the validated category_id instead of hardcoded 1
            'author_id'=> $profile->profile_id, // Use the profile_id from UserProfiles
            'status' => $validated['status'],
            'view_count' => 0,
            'like_count' => 0,
            'comment_count' => 0,
            // 'creater_at' => ($validated['status'] === 'draft') ? now() : null,
            'published_at' => ($validated['status'] === 'published') ? now() : null,   
        ]);

        // Redirect with success message
        $message = ($validated['status'] === 'published') ? 'Article published successfully!' : 'Article saved as draft successfully!';
        return redirect()->route('dashboard.articles')
            ->with('success', $message);
    }

    //editing an articles
    public function edit($article_id)
    {
        // Gate::authorize('accessDashboard', Auth::user());
        $article = Article::findOrFail($article_id);
        // Validate the user is the writer of this article
        $user = Auth::id();
        $profile = UserProfiles::where('user_id', $user)->first();
        if (!$profile || $article->author_id !== $profile->profile_id) {
            return redirect()->route('dashboard.articles')->with('error', 'You are not authorized to edit this article.');
        }
        // dd($article->author_id);
        if (!$article) 
        {
            return redirect()->route('dashboard.articles')->with('error', 'Article not found.');
        }
        // Return the edit view with the article data if it exests
        $categories = \App\Models\Categorie::orderBy('name')->get(); ; // Fetch categories
        return view('dashboard.articles.edit', [
            'article' => $article,
            'categories' => $categories, 
        ]);
    }

    //updating an article
    public function update(Request $request, $article_id){
        //authinticate the user
        Gate::authorize('accessDashboard', Auth::user());

        //geting the article by id
        $article = Article::findOrFail($article_id);

        if (!$article) {
            return redirect()->route('dashboard.articles')->with('error', 'Article not found.');
        }
        //validate the user is the writer of this article
        $user = Auth::id();
        $profile = UserProfiles::where('user_id',$user)->first();
        if (!$profile || $article->author_id !== $profile->profile_id) {
            return redirect()->route('dashboard.articles')->with('error', 'You are not authorized to edit this article.');
        }
        //validate the inputs
        $validator = $this->validating($request);

        if($validator->fails()){
            return redirect()->route('dashboard.articles.edit', ['article' => $article_id])
                ->withErrors($validator)
                ->withInput();
        }
        // dd($request->all());
        // Get validated data if validation passes
        $validated = $validator->validated(); //validated data after validation passes

        $updateData = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'summary' => $validated['summary'] ?? substr(strip_tags($validated['content']), 0, 150),
            'category_id' => $validated['category_id'], // Use validated category_id
            'status' => $validated['status'],
            // Initialize with current image URL; will be updated if new image/removal
            'featured_image_url' => $article->featured_image_url,
            'updated_at' => now(),
        ];

        // dd($updateData);

        if ($request->hasFile('featured_image')) {
            // Delete old image if it exists
            if ($article->featured_image_url && Storage::disk('public')->exists($article->featured_image_url)) {
                Storage::disk('public')->delete($article->featured_image_url);
            }
            $imageFile = $request->file('featured_image');
            $filename = time() . '_' . $imageFile->getClientOriginalName();
            $newImagePath = 'featured_images/' . $filename;
            $imageFile->storeAs('featured_images', $filename, 'public');
            $updateData['featured_image_url'] = $newImagePath;
        } elseif ($request->input('remove_featured_image') == '1' && $article->featured_image_url) {
            // Handle image removal if 'remove_featured_image' is checked and an image exists
            if (Storage::disk('public')->exists($article->featured_image_url)) {
                Storage::disk('public')->delete($article->featured_image_url);
            }
            $updateData['featured_image_url'] = null;
        }
        // dd($newImagePath);

        // Set published_at only if status is 'published' and it wasn't published before.
        if ($validated['status'] === 'published' && is_null($article->published_at)) {
            $updateData['published_at'] = now();
        }
        // Update the article
        $article->update($updateData);

        // Redirect with success message
        $message = ($validated['status'] === 'published') ? 'Article Edited successfully!' : 'Article saved as draft successfully!';
        return redirect()->route('dashboard.articles')
            ->with('success', $message);
    }

    public function destroy($article_id){
        $article = Article::findOrFail($article_id);
        //authinticate the user
        if(!$article) {
            return redirect()->route('dashboard.articles')->with('error', 'Article not found.');
        }
        // Check if the authenticated user is the author of the article
        $user = Auth::id();
        $profile = UserProfiles::where('user_id', $user)->first();
        if (!$profile || $article->author_id !== $profile->profile_id) {
            return redirect()->back()->with('error', 'You are not authorized to delete this article.');
        }

        //delete imagese 
        if ($article->featured_image_url && Storage::disk('public')->exists($article->featured_image_url)) {
            Storage::disk('public')->delete($article->featured_image_url);
        }


        //Delet the artilcle
        $article->delete();
        // return redirect()->back()->with('success','Article Deleted Successfully');
        return redirect()->route('dashboard.articles')->with('success','Article Deleted Successfully');
    }

    // Function to validate the article data
    public function validating(Request $request)
    {
        // Define validation rules
        $rules = [
            'title' => 'required|min:10|max:100',
            'content' => 'required',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webP|max:2048', // 2MB Max
            'status' => 'required|in:published,draft,scheduled',
            'summary' => 'required|max:300|min:30',
            'category_id' => 'required|exists:categories,category_id', 
        ];

        // Validate the request data
        return Validator::make($request->all(), $rules);
    }
    
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            $validator = Validator::make($request->all(), [
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
            ]);

            if ($validator->fails()) {
                // Return a JSON error response that TinyMCE can understand
                return response()->json(['error' => ['message' => $validator->errors()->first('file')]], 422);
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/articles_content', $filename, 'public');
            
            // TinyMCE expects a JSON response with a "location" key
            return response()->json(['location' => Storage::url($path)]);
        }
        // Return a JSON error response
        return response()->json(['error' => ['message' => 'No file uploaded.']], 400);
    }

    public function relatedArticles($article_id, $user_id)
    {
        $relatedArticles = Article::where('author_id', $user_id)
            ->where('article_id', '!=', $article_id) // Exclude the current article
            ->where('status', 'published')
            ->with(['author.user', 'categorie'])
            ->orderByDesc('created_at')
            ->take(6) // Limit to 5 related articles
            ->get();

        return  $relatedArticles ;
    }

public function toggleLike(Request $request, $article_id)
{
    // Check if user is authenticated
    if (!Auth::check()) {
        return response()->json(['error' => 'User must be logged in'], 401);
    }
    
    $user_id = Auth::id();
    $article = Article::findOrFail($article_id);
    
    // Check if user already liked this article
    $existingLike = ArticleLike::where('user_id', $user_id)
                              ->where('article_id', $article_id)
                              ->first();
    
    if ($existingLike) {
        // User already liked the article, so unlike it
        $existingLike->delete();
        $article->decrement('like_count');
        $liked = false;
    } else {
        // User hasn't liked the article, so add a like
        ArticleLike::create([
            'user_id' => $user_id,
            'article_id' => $article_id
        ]);
        $article->increment('like_count');
        $liked = true;
    }
    
    return response()->json([
        'liked' => $liked,
        'like_count' => $article->like_count
    ]);
}

public function like($article_id)
{
    if (!Auth::check()) {
        return response()->json(['success' => false, 'message' => 'User must be logged in'], 401);
    }
    
    $user_id = Auth::id();
    $article = Article::findOrFail($article_id);
    
    $existingLike = ArticleLike::where('user_id', $user_id)
                              ->where('article_id', $article_id)
                              ->first();
    
    if (!$existingLike) {
        ArticleLike::create([
            'user_id' => $user_id,
            'article_id' => $article_id
        ]);
        $article->increment('like_count');
    }
    
    return response()->json([
        'success' => true,
        'like_count' => $article->like_count,
        'message' => 'Article liked!'
    ]);
}

public function unlike($article_id)
{
    if (!Auth::check()) {
        return response()->json(['success' => false, 'message' => 'User must be logged in'], 401);
    }
    
    $user_id = Auth::id();
    $article = Article::findOrFail($article_id);
    
    $existingLike = ArticleLike::where('user_id', $user_id)
                              ->where('article_id', $article_id)
                              ->first();
    
    if ($existingLike) {
        $existingLike->delete();
        $article->decrement('like_count');
    }
    
    return response()->json([
        'success' => true,
        'like_count' => $article->like_count,
        'message' => 'Article unliked!'
    ]);
}

/**
 * Save an article for a user
 *
 * @param \App\Models\Article $article
 * @return \Illuminate\Http\JsonResponse
 */
public function save(Article $article)
{
    $user = Auth::user();
    
    // Check if already saved
    $alreadySaved = \App\Models\userSavedArticle::where('user_id', $user->user_id)
                                            ->where('article_id', $article->article_id)
                                            ->exists();
    
    if ($alreadySaved) {
        return response()->json([
            'success' => false,
            'message' => 'You have already saved this article.'
        ], 422);
    }

    // Save the article
    \App\Models\userSavedArticle::create([
        'user_id' => $user->user_id,
        'article_id' => $article->article_id,
        'saved_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Article saved successfully.',
        'is_saved' => true
    ]);
}

/**
 * Unsave an article for a user
 *
 * @param \App\Models\Article $article
 * @return \Illuminate\Http\JsonResponse
 */
public function unsave(Article $article)
{
    $user = Auth::user();
    
    $savedArticle = \App\Models\userSavedArticle::where('user_id', $user->user_id)
                                           ->where('article_id', $article->article_id)
                                           ->first();
    
    if (!$savedArticle) {
        return response()->json([
            'success' => false,
            'message' => 'This article is not in your saved list.'
        ], 422);
    }

    $savedArticle->delete();

    return response()->json([
        'success' => true,
        'message' => 'Article removed from saved list.',
        'is_saved' => false
    ]);
}
}