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
use App\Mail\NewsletterEmail;

// // Test emailing route - sends to motx98990@gmail.com
// Route::get('/test-newsletter-email', function () {
//     $newsletterData = [
//         'title' => 'Weekly Tech Update - AI Innovations',
//         'summary' => 'Discover the latest breakthroughs in artificial intelligence and how they\'re shaping our future. This week we cover new developments in machine learning, robotics, and more.',
//         'content' => '
//             <h2>🚀 This Week\'s Highlights</h2>
//             <p>Hello there! Welcome to another exciting edition of our weekly newsletter. This week, we\'re diving deep into the fascinating world of artificial intelligence and its revolutionary impact on various industries.</p>
            
//             <h3>🧠 AI Breakthrough: GPT-4 Vision</h3>
//             <p>OpenAI has released GPT-4 Vision, a groundbreaking multimodal AI that can understand and analyze images alongside text. This development opens up incredible possibilities for content creation, medical diagnosis, education, and accessibility.</p>
            
//             <ul>
//                 <li><strong>Content Creation:</strong> Automatic image descriptions and alt-text generation</li>
//                 <li><strong>Medical Diagnosis:</strong> AI-assisted analysis of medical imagery</li>
//                 <li><strong>Education:</strong> Interactive learning with visual content understanding</li>
//                 <li><strong>Accessibility:</strong> Better tools for visually impaired users</li>
//             </ul>
            
//             <h3>💼 Industry Spotlight: AI in Healthcare</h3>
//             <p>The healthcare industry is experiencing a revolutionary transformation thanks to artificial intelligence. From diagnostic imaging to drug discovery, AI is making healthcare more accurate, efficient, and accessible.</p>
//         ',
//         'newsletter_type' => 'weekly',
//         'featured_image' => 'https://via.placeholder.com/600x250/667eea/ffffff?text=Featured+Image'
//     ];

//     $subscriber = (object) [
//         'name' => 'Test User',
//         'email' => 'naief.moatamen@etu.univ-mosta.dz',
//         'unsubscribe_token' => 'demo-token',
//         'preferences_token' => 'demo-token'
//     ];

//     return new \App\Mail\NewsletterEmail($newsletterData, $subscriber);
// })->name('test.newsletter.email');

// // Route to actually SEND the test email to motx98990@gmail.com
// Route::get('/send-test-newsletter-email', function () {
//     $newsletterData = [
//         'title' => 'Weekly Tech Update - AI Innovations',
//         'summary' => 'Discover the latest breakthroughs in artificial intelligence and how they\'re shaping our future. This week we cover new developments in machine learning, robotics, and more.',
//         'content' => '
//             <h2>🚀 This Week\'s Highlights</h2>
//             <p>Hello there! Welcome to another exciting edition of our weekly newsletter. This week, we\'re diving deep into the fascinating world of artificial intelligence and its revolutionary impact on various industries.</p>
            
//             <h3>🧠 AI Breakthrough: GPT-4 Vision</h3>
//             <p>OpenAI has released GPT-4 Vision, a groundbreaking multimodal AI that can understand and analyze images alongside text. This development opens up incredible possibilities for content creation, medical diagnosis, education, and accessibility.</p>
            
//             <ul>
//                 <li><strong>Content Creation:</strong> Automatic image descriptions and alt-text generation</li>
//                 <li><strong>Medical Diagnosis:</strong> AI-assisted analysis of medical imagery</li>
//                 <li><strong>Education:</strong> Interactive learning with visual content understanding</li>
//                 <li><strong>Accessibility:</strong> Better tools for visually impaired users</li>
//             </ul>
            
//             <h3>💼 Industry Spotlight: AI in Healthcare</h3>
//             <p>The healthcare industry is experiencing a revolutionary transformation thanks to artificial intelligence. From diagnostic imaging to drug discovery, AI is making healthcare more accurate, efficient, and accessible.</p>
//         ',
//         'newsletter_type' => 'weekly',
//         'featured_image' => 'https://via.placeholder.com/600x250/667eea/ffffff?text=Featured+Image'
//     ];

//     $subscriber = (object) [
//         'name' => 'Test User',
//         'email' => 'naief.moatamen@etu.univ-mosta.dz',
//         'unsubscribe_token' => 'demo-token',
//         'preferences_token' => 'demo-token'
//     ];

//     // Actually send the email
//     Mail::to('naief.moatamen@etu.univ-mosta.dz')->send(new \App\Mail\NewsletterEmail($newsletterData, $subscriber));

//     return 'Test newsletter email sent successfully to: motx98990@gmail.com';
// })->name('send.test.newsletter.email');







// articles routs
Route::get('/articles',[ArticlesController::class, 'index'])->name('articles');
Route::get('/articles/search', [ArticlesController::class, 'search'])->name('articles.search'); // New route for search
// to show the article by it's id
Route::get('/articles/{article:article_id}', [ArticlesController::class, 'show'])->name('articles.show');
// article rout that uses the medal ware 
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
    //dashboard home and index
    Route::get('/dashboard',[DashboardController::class ,'index'])->name('dashboard');
    Route::get('/dashboard/articles/articles',[DashboardController::class ,'articles'])->name('dashboard.articles');

    // for the article management
    Route::get('/dashboard/articles/create', [ArticlesController::class, 'create'])->name('articles.create');// createing and acrticle 
    Route::post('/dashboard/articles',       [ArticlesController::class, 'store'])->name('articles.store');// storing the article 
    Route::post('/dashboard/upload-image',   [ArticlesController::class, 'uploadImage'])->name('articles.upload-image');// uploading the images
    Route::get('/dashboard/articles/edit/{article:article_id}',     [ArticlesController::class, 'edit'])->name('articles.edit');//editing an article 
    Route::patch('/dashboard/articles/update/{article:article_id}', [ArticlesController::class, 'update'])->name('articles.update');// save the edite
    Route::delete('/dashboard/articles/{article_id}', [ArticlesController::class, 'destroy'])->name('articles.destroy');//deleting an article 
    Route::patch('articles/{article}/publish',        [ArticlesController::class, 'publish'])->name('articles.publish');//publish an article


    //for the newsletter management
    Route::get('/dashboard/newsletter/newsletter',[NewsLetterController::class, 'newsletter'])->name('dashboard.newsletter');
    Route::get('/dashboard/newsletter/create', [NewsLetterController::class, 'create'])->name('newsletter.create'); //create a email newsletter
    Route::get('/newsletters/{id}', [NewsLetterController::class, 'show'])->name('newsletters.show');//show a newsletter by id

    Route::post('/dashboard/newsletter', [NewsLetterController::class, 'store'])->name('newsletter.store');// storing the newsletter
    Route::get('/dashboard/newsletter/edit/{newsletter_id}', [NewsLetterController::class, 'edit'])->name('newsletter.edit');// editing a newsletter
    Route::patch('/dashboard/newsletter/update/{newsletter_id}', [NewsLetterController::class, 'update'])->name('newsletter.update');// update newsletter
    Route::delete('/dashboard/newsletter/{newsletter_id}', [NewsLetterController::class, 'destroy'])->name('newsletter.destroy');// delete newsletter
    Route::post('/dashboard/newsletter/{newsletter_id}/send', [NewsLetterController::class, 'send'])->name('newsletter.send');// send newsletter
    Route::post('/dashboard/newsletter/{newsletter_id}/schedule', [NewsLetterController::class, 'schedule'])->name('newsletter.schedule');// schedule newsletter

    // Author profile management routes
    Route::post('/profile/author/update', [ProfilesController::class, 'updateAuthorProfile'])->name('author.profile.update');
    Route::delete('/profile/author/delete', [ProfilesController::class, 'deleteAuthorProfile'])->name('author.profile.delete');
});

//route for the profile.show for author
Route::get('/profile/{profileID}', [ProfilesController::class, 'show'])->name('profile.show');
//profile route
Route::middleware(['auth'])->group(function () {
    // Main profile route - shows appropriate profile based on user type
    Route::get('/profile', [ProfilesController::class, 'index'])
        ->can('accessProfile') 
        ->name('profile');

    // Reader profile management routes
    Route::post('/profile/update', [ProfilesController::class, 'updateReaderProfile'])
        ->can('accessProfile') 
        ->name('profile.update');
    Route::delete('/profile/delete', [ProfilesController::class, 'deletReaderProfile'])
        ->can('accessProfile') 
        ->name('profile.delete');
    
    // Author profile route (for when authors want to view their own profile)
    Route::get('/profile/author', [ProfilesController::class, 'authorProfile'])
        ->can('accessProfile')
        ->name('author.profile.show');
});

// // route to the profile page
// Route::get('/profile',[ProfilesController::class,'index'])
//                       ->middleware('auth')  
//                       ->can('accessProfile') 
//                       ->name('profile');



// //reader profile
// Route::get('/profile/reader_profile',[ProfilesController::class,'index'])
//                       ->middleware('auth')  
//                       ->can('accessProfile') 
//                       ->name('reader_profile');

// //edit reader profile 
// Route::post('/profile/update', [ProfilesController::class, 'updateReaderProfile'])
//                       ->middleware('auth')  
//                       ->can('accessProfile') 
//                       ->name('profile.update');
// //delet reader acount 
// Route::delete('/profile/delete', [ProfilesController::class, 'deletReaderProfile'])
//                       ->middleware('auth')
//                       ->can('accessProfile') 
//                       ->name('profile.delete');




















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



//contact route
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit'); //for when submeted
//about us route
Route::get('/about', function () { return view('about_us.about_us'); })->name('about');

