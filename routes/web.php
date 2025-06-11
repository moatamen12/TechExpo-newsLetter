<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

Route::get('/preview/newsletter-email', function(){
    return view('mail.newsletter-email', [
        'newsletter' => (object)[
            'title' => 'Weekly Tech Digest - Preview',
            'content' => '<h2>Latest Tech Insights</h2>
            <p>Welcome to this week\'s edition of TechExpo Newsletter! We\'ve curated the most exciting developments in technology and science just for you.</p>
            
            <h3>This Week\'s Highlights</h3>
            <ul>
                <li>Revolutionary AI breakthroughs changing the industry</li>
                <li>New sustainable tech innovations</li>
                <li>Latest developments in quantum computing</li>
                <li>Emerging trends in cybersecurity</li>
            </ul>
            
            <p>Stay ahead of the curve with our expertly curated content, designed to keep you informed about the rapidly evolving world of technology.</p>',
            'created_at' => now(),
            'newsletter_type' => 'Weekly',
            'featured_image' => 'default-newsletter.jpg',
            'catagorie' => 'technology'
        ],
        'author' => (object)[
            'name' => 'Tech Expert',
            'id' => 1,
            'userProfile' => (object)[
                'profile_photo' => 'default-avatar.jpg',
                'title' => 'Technology Writer & Expert',
                'bio' => 'Passionate about technology and innovation, bringing you the latest insights from the tech world.'
            ]
        ]
    ]);
})->name('preview.newsletter');

// Preview newsletter success email template
Route::get('/preview/newsletter-success', function(){
    return view('mail.newsletter-sent-succesfuly', [
        'newsletter' => (object)[
            'title' => 'Weekly Tech Digest - Successfully Sent',
            'sent_at' => now(),
            'newsletter_type' => 'Weekly',
            'catagorie' => 'Technology'
        ],
        'author' => (object)[
            'name' => 'Tech Expert',
            'id' => 1
        ],
        'stats' => [
            'total_recipients' => 1250,
            'sent_successfully' => 1237,
            'failed_deliveries' => 13
        ]
    ]);
})->name('preview.newsletter.success');
//send it
Route::get('/test-send-newsletter', function(){
    try {
        Mail::send('mail.newsletter-email', [
            'newsletter' => (object)[
                'title' => 'Weekly Tech Digest - Test Email',
                'content' => '<h2>Latest Tech Insights</h2>
                <p>Welcome to this week\'s edition of TechExpo Newsletter! We\'ve curated the most exciting developments in technology and science just for you.</p>
                
                <h3>This Week\'s Highlights</h3>
                <ul>
                    <li>Revolutionary AI breakthroughs changing the industry</li>
                    <li>New sustainable tech innovations</li>
                    <li>Latest developments in quantum computing</li>
                    <li>Emerging trends in cybersecurity</li>
                </ul>
                
                <p>Stay ahead of the curve with our expertly curated content, designed to keep you informed about the rapidly evolving world of technology.</p>',
                'created_at' => now(),
                'newsletter_type' => 'Weekly',
                'featured_image' => 'default-newsletter.jpg',
                'catagorie' => 'technology'
            ],
            'author' => (object)[
                'name' => 'Tech Expert',
                'id' => 1,
                'userProfile' => (object)[
                    'profile_photo' => 'default-avatar.jpg',
                    'title' => 'Technology Writer & Expert',
                    'bio' => 'Passionate about technology and innovation, bringing you the latest insights from the tech world.'
                ]
            ]
        ], function($message) {
            $message->to('motx98990@gmail.com', 'Test Recipient')
                   ->subject('TechExpo Newsletter - Test Email')
                   ->from(config('mail.from.address'), config('mail.from.name'));
        });
        
        return 'Test newsletter email sent successfully to motx98990@gmail.com!';
    } catch (Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
})->name('test.newsletter.send');





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
    //subscribers management
    Route::get('/dashboard/subscribers', [NewsLetterController::class, 'subscribers'])->name('dashboard.subscribers');
    Route::delete('/dashboard/subscribers/{id}', [NewsLetterController::class, 'removeSubscriber'])->name('subscriber.remove');
    Route::delete('/dashboard/subscribers/bulk-remove', [NewsLetterController::class, 'bulkRemoveSubscribers'])->name('subscribers.bulk-remove');
    Route::get('/dashboard/subscribers/export', [NewsLetterController::class, 'exportSubscribers'])->name('dashboard.subscribers.export');



    // Newsletter preview route
    Route::get('/newsletter/{id}/preview', [NewsLetterController::class, 'preview'])->name('newsletter.preview');
    // Newsletter send route
    Route::post('/newsletter/{id}/send', [NewsLetterController::class, 'sendNewsletter'])->name('newsletter.send');
    //for the newsletter management
    Route::get('/dashboard/newsletter/newsletter',[NewsLetterController::class, 'newsletter'])->name('dashboard.newsletter');
    Route::get('/dashboard/newsletter/create', [NewsLetterController::class, 'create'])->name('newsletter.create'); //create a email newsletter
    Route::get('/newsletters/{newsletter}', [NewsLetterController::class, 'show'])->name('newsletter.show');//show a newsletter by id

    Route::post('/dashboard/newsletter', [NewsLetterController::class, 'store'])->name('newsletter.store');// storing the newsletter
    Route::get('/newsletter/{newsletter}/edit', [NewsLetterController::class, 'edit'])->name('newsletter.edit');// editing a newsletter
    Route::put('/newsletter/{newsletter}', [NewsLetterController::class, 'update'])->name('newsletter.update');// update newsletter
    Route::delete('/newsletter/{newsletter}', [NewsLetterController::class, 'destroy'])->name('newsletter.destroy');// delete newsletter

    // Newsletter send options and confirmation
    Route::get('/newsletter/{newsletter}/send-options', [NewsLetterController::class, 'sendOptions'])->name('newsletter.send-options');
    Route::post('/newsletter/{newsletter}/send-confirm', [NewsLetterController::class, 'sendConfirm'])->name('newsletter.send.confirm');
    Route::get('/newsletter/{newsletter}/test-send', [NewsLetterController::class, 'testSend'])->name('newsletter.test-send');
    Route::post('/newsletter/{newsletter}/send', [NewsLetterController::class, 'send'])->name('newsletter.send');


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

// Test mailable directly (synchronous email sending)
Route::get('/test-mailable', function(){
    try {
        $newsletter = \App\Models\Newsletter::first() ?? (object)[
            'title' => 'Test Newsletter',
            'content' => '<h2>Test Content</h2><p>This is a test.</p>',
            'catagorie' => 'technology',
            'newsletter_type' => 'Weekly',
            'created_at' => now(),
            'featured_image' => 'default-newsletter.jpg'
        ];
        
        $author = (object)[
            'name' => 'Test Author',
            'id' => 1,
            'userProfile' => (object)[
                'profile_photo' => 'default-avatar.jpg',
                'title' => 'Test Title',
                'bio' => 'Test bio'
            ]
        ];
        
        Mail::to('motx98990@gmail.com')
            ->send(new \App\Mail\NewsletterEmail($newsletter, $author));
        
        return 'Newsletter sent successfully (synchronous)!';
    } catch (Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});



