<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\newsletterController;
use App\Http\Controllers\ReaderEnteractions;

// test emailing route
// Route::get('/test-email',function(){
//     $name = "test email";
//     Mail::to('motx98990@gmail.com')->send(new \App\Mail\NewsletterEmail($name));

// });




//rout to the subscribe
Route::get('/subscribe',[SubscribeController::class, 'create'])->name('subscribe');
Route::post('/subscribe',[SubscribeController::class, 'store'])->name('subscribe.submit');;
//rout to the login
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login',[LoginController::class, 'store'])->name('login.submit');;
//rput to logout
Route::post('logout', [LoginController::class, 'destroy'])->name('logout');




//index route
Route::get('/', [HomeController::class, 'index'])->name('home');
//home partials routes
Route::get('/home/partials', [HomeController::class, 'homePartials'])->name('home.partials');
Route::get('/load-more-articles', [App\Http\Controllers\HomeController::class, 'loadMoreArticles'])->name('home.loadMoreArticles');




// articles routs
Route::get('/articles',[ArticlesController::class, 'index'])->name('articles');
Route::get('/articles/search', [ArticlesController::class, 'search'])->name('articles.search'); // New route for search
// to show the article by it's id
Route::get('/articles/{article:article_id}', [ArticlesController::class, 'show'])->name('articles.show');

Route::middleware('auth')->group(function () {
    // Comments routes
    Route::post('/comments/{article:article_id}', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment_id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment_id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Follow/Unfollow routes
    Route::post('/follow/{profile}', [ReaderEnteractions::class, 'follow'])->name('interactions.profiles.follow');
    Route::delete('/unfollow/{profile}', [ReaderEnteractions::class, 'unfollow'])->name('interactions.profiles.unfollow');
    
    // Like/Unlike routes
    Route::post('/articles/{article}/like', [ArticlesController::class, 'like'])->name('articles.like');
    Route::post('/articles/{article}/unlike', [ArticlesController::class, 'unlike'])->name('articles.unlike');
    
    // Save/Unsave routes
    Route::post('/articles/{article}/save', [ArticlesController::class, 'save'])->name('articles.save');
    Route::post('/articles/{article}/unsave', [ArticlesController::class, 'unsave'])->name('articles.unsave');
});


//dashboard Routes
Route::middleware(['auth', 'checkUserProfile.dashboard'])->group(function () {
    Route::get('/dashboard',[DashboardController::class ,'index'])->name('dashboard');
    Route::get('/dashboard/articles',[DashboardController::class ,'articles'])->name('dashboard.articles');

    Route::get('/dashboard/articles/create', [ArticlesController::class, 'create'])->name('articles.create');// createing and acrticle 
    Route::post('/dashboard/articles', [ArticlesController::class, 'store'])->name('articles.store');// storing the article 
    Route::post('/dashboard/upload-image', [ArticlesController::class, 'uploadImage'])->name('articles.upload-image');// uploading the images

    Route::get('/dashboard/articles/edit/{article:article_id}', [ArticlesController::class, 'edit'])->name('articles.edit');//editing an article 
    Route::patch('/dashboard/articles/update/{article:article_id}', [ArticlesController::class, 'update'])->name('articles.update');// save the edite
    Route::delete('/dashboard/articles/{article_id}', [ArticlesController::class, 'destroy'])->name('articles.destroy');//deleting an article 
    
    Route::get('./dashboard/newsletter',[newsletterController::class,'create'])->name('newsletter.create'); //create newsletter
    
    // Route::post('/articles/{article_id}/toggle-like', [App\Http\Controllers\ArticlesController::class, 'toggleLike'])->name('articles.toggle-like');

});

// route to the profile page
Route::get('/profile',[ProfilesController::class,'index'])
                      ->middleware('auth')  
                      ->can('accessProfile') 
                      ->name('profile');
//route for the profile.show for author
Route::get('/profile/{profileID}', [ProfilesController::class, 'show'])
    ->middleware('auth')
    ->can('accessProfile')
    ->name('profile.show');
    

//reader profile
Route::get('/profile/reader_profile',[ProfilesController::class,'index'])
                      ->middleware('auth')  
                      ->can('accessProfile') 
                      ->name('reader_profile');

//edit reader profile 
Route::post('/profile/update', [ProfilesController::class, 'updateReaderProfile'])
                      ->middleware('auth')  
                      ->can('accessProfile') 
                      ->name('profile.update');
//delet reader acount
Route::delete('/profile/delete', [ProfilesController::class, 'deletReaderProfile'])
                      ->middleware('auth')
                      ->can('accessProfile') 
                      ->name('profile.delete');





//contact route
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit'); //for when submeted
//about us route
Route::get('/about', function () { return view('about_us.about_us'); })->name('about');