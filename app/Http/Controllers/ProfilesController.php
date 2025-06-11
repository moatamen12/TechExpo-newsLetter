<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userSavedArticle;
use App\Models\User;
use App\Models\Article;
use App\Models\userFollower; 
use App\Models\Contact;
use App\Models\ArticleLike; 
use App\Models\UserProfiles;
use App\Models\SocialLink; // Add this line for SocialLink model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;


class ProfilesController extends Controller
{

    public function index()
    {
        //get the authenticated user
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }
        
        // Check if user has a profile (is an author)
        if ($user->userProfile) {
            return $this->authorProfile($user);
        } else {
            return $this->readerProfile($user);
        }
    }
    
    //reader profile
    public function readerProfile(User $user) 
    {
        $savedUserArticles = userSavedArticle::with([
            'article' => function ($query) {
                $query->with([
                    'author' => function ($subQuery) {
                        $subQuery->with('user'); // This loads the User model (author) via UserProfile
                    },
                    'categorie' // Article model has a 'categorie' relationship
                ]);
            }
        ])
        ->where('user_id', $user->user_id) 
        ->latest('saved_at') // Assuming 'saved_at' is the correct column in user_saved_articles
        ->paginate(5);

        $followedWritersModels = userFollower::getFollowedWriters($user->user_id)->get();
        
        return view('profile.reader_profile', [                
            'user_name' => $user->name,
            'user_email' => $user->email,
            'savedArticles' => $savedUserArticles, 
            'followedWriters' => $followedWritersModels, // Pass followed writers to the view
            'followers' => userFollower::where('following_id', $user->user_id)->count(),
        ]);
    }

    //author profile
    public function authorProfile(User $user) 
    {
        // Get followers of this author
        $followers = userFollower::with('follower')
            ->where('following_id', $user->userProfile->profile_id)
            ->get();

        // Get author's articles
        $articles = Article::with(['categorie'])
            ->where('author_id', $user->userProfile->profile_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get author's saved articles
        $savedArticles = userSavedArticle::with([
            'article' => function ($query) {
                $query->with([
                    'author' => function ($subQuery) {
                        $subQuery->with('user');
                    },
                    'categorie'
                ]);
            }
        ])
        ->where('user_id', $user->user_id)
        ->latest('saved_at')
        ->get();

        // Get users that this author is following
        $following = userFollower::with(['following' => function($query) {
            $query->with('user');
        }])
        ->where('follower_id', $user->user_id)
        ->get();

        return view('profile.author_profile', [
            'user' => $user,
            'followers' => $followers,
            'followersCount' => $followers->count(),
            'articles' => $articles,
            'articlesCount' => $articles->count(),
            'savedArticles' => $savedArticles,
            'savedArticlesCount' => $savedArticles->count(),
            'following' => $following,
            'followingCount' => $following->count(),
        ]);
    }

    public function show($profile_id){

        $authorProfile = UserProfiles::where('profile_id', $profile_id)->first();

        if (!$authorProfile) {
            return redirect()->back()->with('error', 'Author profile not found.');
        }

        $authorUser = User::with('userProfile')
                           ->find($authorProfile->user_id); 
        if (!$authorUser) {
            return redirect()->back()->with('error', 'User not found for this profile.');
        }
        
        $articles = Article::with(['author.user', 'categorie'])
            ->where('author_id', $authorProfile->profile_id)
            ->where('status', 'published')
            // Order by the sum of likes and comments in descending order
            ->orderByRaw('(IFNULL(like_count, 0) + IFNULL(comment_count, 0)) DESC')
            // Take the top 5 articles
            ->take(5)
            ->get();

        return view('profile.show', [
            'author' => $authorUser, 
            'articles' => $articles,
            'followers' => userFollower::where('following_id', $authorProfile->profile_id)->count(),
        ]);
    }

    public function updateReaderProfile(Request $request)
    {
        $user = Auth::user();

        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'min:5','max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'), // Ignore current user's email
            ],
            'old_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', Password::min(8)->sometimes(), 'confirmed'],
            // new_password_confirmation is handled by 'confirmed' rule on new_password
        ];

        // Custom validation messages
        $messages = [
            'new_password.confirmed' => 'The new password confirmation does not match.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'old_password.current_password' => 'The provided old password does not match your current password.',
        ];

        // Conditionally add password validation if old_password is provided
        if ($request->filled('old_password') || $request->filled('new_password')) {
            $rules['old_password'] = ['required', 'string', 'current_password']; // Uses Laravel's built-in current_password rule
            $rules['new_password'] = ['required', 'string', Password::min(8), 'confirmed'];
        }

        $validatedData = $request->validate($rules, $messages);

        // Update name and email
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Update password if new_password is provided and old_password was correct
        if ($request->filled('new_password') && $request->filled('old_password')) {
            // The 'current_password' rule already validated the old_password.
            // If validation passes, we can safely update.
            $user->password = Hash::make($validatedData['new_password']);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    public function updateAuthorProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'work' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'old_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', Password::min(8)->sometimes(), 'confirmed'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url', 'max:255'],
            'social_active' => ['nullable', 'array'],
        ];

        $messages = [
            'new_password.confirmed' => 'The new password confirmation does not match.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'old_password.current_password' => 'The provided old password does not match your current password.',
        ];

        // Conditionally add password validation
        if ($request->filled('old_password') || $request->filled('new_password')) {
            $rules['old_password'] = ['required', 'string', 'current_password'];
            $rules['new_password'] = ['required', 'string', Password::min(8), 'confirmed'];
        }

        $validatedData = $request->validate($rules, $messages);

        // Update user basic info
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Update password if provided
        if ($request->filled('new_password') && $request->filled('old_password')) {
            $user->password = Hash::make($validatedData['new_password']);
        }

        $user->save();

        $profile = $user->userProfile;
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($profile->profile_photo && Storage::disk('public')->exists($profile->profile_photo)) {
                Storage::disk('public')->delete($profile->profile_photo);
            }
            
            // Store new photo
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $profile->profile_photo = $profilePhotoPath;
        }

        $profile->bio = $validatedData['bio'] ?? $profile->bio;
        $profile->work = $validatedData['work'] ?? $profile->work;
        $profile->website = $validatedData['website'] ?? $profile->website;

        $profile->save();

        // Handle social links
        if ($request->has('social_links')) {
            foreach ($request->social_links as $platform => $url) {
                if (!empty($url)) {
                    $isActive = isset($request->social_active[$platform]) ? true : false;
                    
                    SocialLink::updateOrCreate(
                        [
                            'user_id' => $user->user_id,
                            'platform' => $platform
                        ],
                        [
                            'url' => $url,
                            'is_active' => $isActive
                        ]
                    );
                } else {
                    // Delete if URL is empty
                    SocialLink::where('user_id', $user->user_id)
                             ->where('platform', $platform)
                             ->delete();
                }
            }
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'min:5','max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'), // Ignore current user's email
            ],
            'old_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', Password::min(8)->sometimes(), 'confirmed'],
            // new_password_confirmation is handled by 'confirmed' rule on new_password
        ];

        // Custom validation messages
        $messages = [
            'new_password.confirmed' => 'The new password confirmation does not match.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'old_password.current_password' => 'The provided old password does not match your current password.',
        ];

        // Conditionally add password validation if old_password is provided
        if ($request->filled('old_password') || $request->filled('new_password')) {
            $rules['old_password'] = ['required', 'string', 'current_password']; // Uses Laravel's built-in current_password rule
            $rules['new_password'] = ['required', 'string', Password::min(8), 'confirmed'];
        }

        $validatedData = $request->validate($rules, $messages);

        // Update name and email
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Update password if new_password is provided and old_password was correct
        if ($request->filled('new_password') && $request->filled('old_password')) {
            // The 'current_password' rule already validated the old_password.
            // If validation passes, we can safely update.
            $user->password = Hash::make($validatedData['new_password']);
        }

        $user->save();

        // Handle social links
        if ($request->has('social_links')) {
            foreach ($request->social_links as $platform => $url) {
                if (!empty($url)) {
                    $isActive = isset($request->social_active[$platform]);
                    
                    SocialLink::updateOrCreate(
                        [
                            'user_id' => $user->user_id,
                            'platform' => $platform
                        ],
                        [
                            'url' => $url,
                            'is_active' => $isActive
                        ]
                    );
                } else {
                    // Delete if URL is empty
                    SocialLink::where('user_id', $user->user_id)
                             ->where('platform', $platform)
                             ->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function deletReaderProfile(Request $request) // Method name as per your existing empty function
    {
        $user = Auth::user();

        $request->validateWithBag('deleteAccount', [ // Using 'deleteAccount' error bag as per your blade file
            'password_delete' => ['required', 'string'],
        ], [
            'password_delete.required' => 'Password is required to delete your account.'
        ]);

        if (!Hash::check($request->password_delete, $user->password)) {
            return redirect()->back()
                             ->withInput() // Keep the form input (though password fields are usually not repopulated for security)
                             ->with('error_delete_account', 'The provided password does not match your current password.') // Session flash for general error display
                             ->withErrors(['password_delete' => 'Incorrect password.'], 'deleteAccount'); // Specific error for the field
        }

        // Optional: Wrap in a database transaction if you have multiple critical delete operations
        // DB::beginTransaction();

        try {
            if (class_exists(Contact::class)) {
                Contact::where('user_id', $user->user_id)->delete();
            }

            // Example: Delete related article likes if ArticleLike model exists
            // and 'user_id' is the foreign key.
            if (class_exists(ArticleLike::class)) {
                ArticleLike::where('user_id', $user->user_id)->delete();
            }
            
            // Note:
            // - UserSavedArticle records are expected to be deleted by ON DELETE CASCADE.
            // - Comment records are expected to be deleted by ON DELETE CASCADE.
            // - UserFollower records (where this user is the follower) are expected to be deleted by ON DELETE CASCADE.
            // - A reader user does not have a UserProfile, so no UserProfile deletion is needed.

            // 2. Log the user out BEFORE deleting their record from the users table.
            Auth::logout();
            // Invalidate the session and regenerate the token after logout.
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 3. Delete the user. This should trigger the ON DELETE CASCADE for related tables.
            $user->delete();

            // DB::commit(); // Commit transaction if used

            return redirect()->route('home')->with('success', 'Your account has been successfully deleted.');

        } catch (\Exception $e) {
            // DB::rollBack(); // Rollback transaction if used

            // Log the error for debugging purposes
            // \Illuminate\Support\Facades\Log::error('Account deletion failed for user ID ' . $user->user_id . ': ' . $e->getMessage());
            
            // Since the user is already logged out, redirecting them home with an error is a safe bet.
            // You might want to add more sophisticated error handling or user notification.
            return redirect()->route('home')->with('error', 'An error occurred while trying to delete your account. Please contact support.');
        }
    }

    public function deleteAuthorProfile(Request $request)
    {
        $user = Auth::user();

        $request->validateWithBag('deleteAccount', [
            'password_delete' => ['required', 'string'],
        ], [
            'password_delete.required' => 'Password is required to delete your account.'
        ]);

        if (!Hash::check($request->password_delete, $user->password)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error_delete_account', 'The provided password does not match your current password.')
                             ->withErrors(['password_delete' => 'Incorrect password.'], 'deleteAccount');
        }

        try {
            // Delete profile photo if exists
            if ($user->userProfile && $user->userProfile->profile_photo) {
                Storage::disk('public')->delete($user->userProfile->profile_photo);
            }

            // Delete related records
            if (class_exists(Contact::class)) {
                Contact::where('user_id', $user->user_id)->delete();
            }

            if (class_exists(ArticleLike::class)) {
                ArticleLike::where('user_id', $user->user_id)->delete();
            }

            // Log out user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Delete user (this should cascade to UserProfile and Articles)
            $user->delete();

            return redirect()->route('home')->with('success', 'Your account has been successfully deleted.');

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'An error occurred while trying to delete your account. Please contact support.');
        }
    }

    /**
     * Show the writer registration form
     */
    public function showWriterForm()
    {
        $user = Auth::user();
        
        // Check if user already has a profile
        if ($user->userProfile) {
            return redirect()->route('dashboard')->with('info', 'You are already a writer!');
        }
        
        return view('profile.become_writer');
    }

    /**
     * Process writer registration
     */
    public function processWriterRegistration(Request $request)
    {
        $user = Auth::user();
        
        // Check if user already has a profile
        if ($user->userProfile) {
            return redirect()->route('dashboard')->with('info', 'You are already a writer!');
        }

        // Validation rules
        $rules = [
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'work' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url', 'max:255'],
            'social_active' => ['nullable', 'array'],
            'skip_details' => ['nullable', 'boolean']
        ];

        $messages = [
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.mimes' => 'Profile photo must be a JPEG, JPG, or PNG file.',
            'profile_photo.max' => 'Profile photo must not exceed 2MB.',
            'bio.max' => 'Bio must not exceed 1000 characters.',
            'work.max' => 'Work field must not exceed 255 characters.',
            'website.url' => 'Website must be a valid URL.',
            'social_links.*.url' => 'Social media links must be valid URLs.',
        ];

        // If user chooses to skip, create minimal profile
        if ($request->has('skip_details')) {
            $this->createMinimalWriterProfile($user);
            return redirect()->route('dashboard')->with('success', 'Welcome to your writer dashboard! You can update your profile details anytime.');
        }

        $validatedData = $request->validate($rules, $messages);

        // Create writer profile with provided details
        $this->createWriterProfile($user, $validatedData, $request);

        return redirect()->route('dashboard')->with('success', 'Welcome to your writer dashboard! Your profile has been created successfully.');
    }

    /**
     * Create minimal writer profile
     */
    private function createMinimalWriterProfile($user)
    {
        UserProfiles::create([
            'user_id' => $user->user_id,
            'profile_photo' => null,
            'bio' => null,
            'work' => null,
            'website' => null,
            'followers_count' => 0,
            'num_articles' => 0,
            'reactions_count' => 0,
            'work' => null
        ]);
    }

    /**
     * Create writer profile with details
     */
    private function createWriterProfile($user, $validatedData, $request)
    {
        // Handle profile photo upload
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // Create user profile
        $profile = UserProfiles::create([
            'user_id' => $user->user_id,
            'profile_photo' => $profilePhotoPath,
            'bio' => $validatedData['bio'],
            'work' => $validatedData['work'],
            'website' => $validatedData['website'],
            'followers_count' => 0,
            'num_articles' => 0,
            'reactions_count' => 0,
            'work' => null
        ]);

        // Handle social links
        if ($request->has('social_links')) {
            foreach ($request->social_links as $platform => $url) {
                if (!empty($url)) {
                    $isActive = isset($request->social_active[$platform]) ? true : false;
                    
                    SocialLink::create([
                        'user_id' => $user->user_id,
                        'platform' => $platform,
                        'url' => $url,
                        'is_active' => $isActive
                    ]);
                }
            }
        }
    }
}