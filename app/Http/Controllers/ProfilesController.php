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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        // Simplified logic: always show readerProfile for now, 
        // you can add author-specific logic later if needed.
        return  $this->readerProfile($user);
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
        // dd($followedWritersModels->profile_id);
        
            // dd($followedWritersModels->first()->following_id);
        
        return view('profile.reader_profile', [                
            'user_name' => $user->name,
            'user_email' => $user->email,
            'savedArticles' => $savedUserArticles, 
            'followedWriters' => $followedWritersModels, // Pass followed writers to the view
            'followers' => userFollower::where('following_id', $user->user_id)->count(),
            // 'following' => userFollower::where('follower_id', $user->user_id)->count(), // Corrected to user_id
        ]);
    }


    public function show($profile_id){

        $authorProfile = UserProfiles::where('profile_id', $profile_id)->first();

        if (!$authorProfile) {
            return redirect()->back()->with('error', 'Author profile not found.');
        }

        $authorUser = User::with('userProfile')
                           ->find($authorProfile->user_id); 
        // dd($authorUser);
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

            // 'following' => userFollower::where('follower_id', $authorProfile->profile_id)->count(),
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
}