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
// to show the article by it's id
Route::get('/articles/{article:article_id}', [ArticlesController::class, 'show'])->name('articles.show');

//newsletter routes
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
    
    Route::post('/articles/{article_id}/toggle-like', [App\Http\Controllers\ArticlesController::class, 'toggleLike'])->name('articles.toggle-like');
});

// Follow/unfollow route - make sure this is defined exactly as shown
Route::post('/users/{profileId}/toggle-follow', [App\Http\Controllers\UserFollowController::class, 'toggleFollow'])
        ->name('users.toggleFollow');
    
// route to the profile page
Route::get('/profile',[ProfilesController::class,'index'])
                      ->middleware('auth')  
                      ->can('accessProfile') 
                      ->name('profile');
//reader profile
Route::get('/profile/reader_profile',[ProfilesController::class,'index'])
                      ->middleware('auth')  
                      ->can('accessProfile') 
                      ->name('reader_profile');




// //edit an article 
// Route::get('/dashboard/articles/edit/{article:article_id}', [ArticlesController::class, 'edit']) //editing an article 
//                         ->middleware('auth') 
//                         ->can('accessDashboard')
//                         ->name('articles.edit');

// // Update an article
// Route::patch('/dashboard/articles/update/{article:article_id}', [ArticlesController::class, 'update']) //save the edit of an  article 
//                         ->middleware('auth') 
//                         ->can('accessDashboard')
//                         ->name('articles.update');

// //Destroy an article
// Route::delete('/dashboard/articles/{article_id}', [ArticlesController::class, 'destroy']) //deleting an article 
//                         ->middleware('auth') 
//                         ->can('accessDashboard')
//                         ->name('articles.destroy');

// // Route::get('/dashboard/articles/edit{article}', [ArticlesController::class, 'edit']) //editing an article 
// //                         ->middleware('auth') 
// //                         ->can('accessDashboard')
// //                         ->name('articles.edit');











//contact route
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit'); //for when submeted
//about us route
Route::get('/about', function () { return view('about_us.about_us'); })->name('about');
//the storing commints route
Route::post('/comments/{article:article_id}', [CommentController::class, 'store'])->name('comments.store');