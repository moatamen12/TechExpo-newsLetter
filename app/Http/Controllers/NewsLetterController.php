<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\UserProfiles;
use App\Models\Categorie;
use App\Models\Subscriber;
use App\Models\UserFollower;
use App\Models\Article; // Add Article model
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\NewsletterEmail;
use App\Mail\NewsletterSentSuccessfully;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsLetterController extends Controller
{
    //get the current user profile id
    public function getCurrentUser(): int // Corrected spelling and return type
    {
        $userId = Auth::id();
        if (!$userId) {
            // This should ideally be caught by auth middleware earlier
            Log::warning('Attempted to get current user profile ID without authenticated user.');
            abort(401, 'User not authenticated.');
        }

        $userProfile = UserProfiles::where('user_id', $userId)->first();

        if (!$userProfile || !isset($userProfile->profile_id)) {
            Log::error('User profile not found or profile_id is missing for user_id: ' . $userId, [
                'user_id' => $userId,
                'user_profile_found' => $userProfile ? 'yes' : 'no',
            ]);
            abort(500, 'User profile configuration error. Please contact support.');
        }
        return (int) $userProfile->profile_id; // Ensure it's an integer
    }

    public function create()
    {
        $categories = Categorie::orderBy('name')->get();
        return view('dashboard.newsletter.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:10|max:100',
            'summary' => 'required|string|min:30|max:300',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,category_id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $profile_id = $this->getCurrentUser();

        // Handle featured image upload
        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . Auth::id() . '_' . $image->getClientOriginalName();
            $imagePath = 'featured_images/' . $filename;
            $image->storeAs('public/featured_images', $filename);
            $imagePath = 'featured_images/' . $filename;
        }

        // Always create as draft and redirect to send-options
        $newsletter = Newsletter::create([
            'title' => $request->title,
            'content' => $request->content,
            'summary' => $request->summary,
            'author_id' => $profile_id,
            'category_id' => $request->category_id,
            'featured_image' => $imagePath,
            'status' => 'draft',
            'scheduled_at' => null,
            'sent_at' => null,
        ]);

        // Always redirect to send-options page
        return redirect()->route('newsletter.send-options', $newsletter->id)
                         ->with('success', 'Newsletter created successfully! Choose what to do next.');
    }

    /**
     * Show send options page
     */
    public function sendOptions(Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            Log::warning('Unauthorized access attempt to newsletter send options.', [
                'newsletter_id' => $newsletter->id,
                'newsletter_author_id' => $newsletter->author_id,
                'current_user_profile_id' => $currentProfileId,
                'auth_user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized access to newsletter');
        }

        // Get follower counts for display - only author's followers
        $allMyFollowers = UserFollower::where('following_id', $newsletter->author_id)->count();
        
        // Get the actual followers for selection if needed
        $myFollowers = UserFollower::with(['follower'])
            ->where('following_id', $newsletter->author_id)
            ->get();
        
        return view('dashboard.newsletter.send-options', compact('newsletter', 'allMyFollowers', 'myFollowers'));
    }

    /**
     * Confirm and send newsletter
     */
    public function sendConfirm(Request $request, Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            abort(403, 'Unauthorized access to newsletter');
        }

        // Validate based on action type
        $rules = [
            'action_type' => 'required|in:send,schedule,draft',
        ];

        // Add conditional validation based on action type
        if ($request->action_type === 'draft') {
            // No additional validation needed for draft
        } else {
            // For send and schedule, we need recipient info
            $rules['recipient_type'] = 'required|in:all_followers,selected_followers,test';
            $rules['selected_followers'] = 'required_if:recipient_type,selected_followers|array';
            
            if ($request->action_type === 'schedule') {
                $rules['scheduled_at'] = 'required|date|after:now';
            }
        }

        $request->validate($rules);

        // Handle different action types
        if ($request->action_type === 'draft') {
            // Just save as draft
            $newsletter->update(['status' => 'draft']);
            
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been saved as a draft.');
                
        } elseif ($request->action_type === 'schedule') {
            // Schedule the newsletter
            $updateData = [
                'status' => 'scheduled',
                'scheduled_at' => $request->scheduled_at,
                'recipient_type' => $request->recipient_type,
            ];

            if ($request->recipient_type === 'selected_followers' && $request->selected_followers) {
                $updateData['selected_subscribers'] = json_encode($request->selected_followers);
            }

            $newsletter->update($updateData);
            
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been scheduled for ' . 
                   \Carbon\Carbon::parse($request->scheduled_at)->format('M j, Y \a\t g:i A'));
                   
        } else {
            // Send immediately
            $updateData = [
                'recipient_type' => $request->recipient_type,
            ];

            if ($request->recipient_type === 'selected_followers' && $request->selected_followers) {
                $updateData['selected_subscribers'] = json_encode($request->selected_followers);
            }

            $newsletter->update($updateData);
            
            try {
                $newsletter->update(['status' => 'sending']);
                $result = $this->sendNewsletterSync($newsletter);
                
                if ($result) {
                    return redirect()->route('dashboard.newsletter')
                        ->with('success', 'Newsletter has been sent successfully!');
                } else {
                    return redirect()->route('dashboard.newsletter')
                        ->with('error', 'Failed to send newsletter. Please check the logs for details.');
                }
            } catch (\Exception $e) {
                Log::error('Error in sendConfirm immediate send', [
                    'newsletter_id' => $newsletter->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $newsletter->update(['status' => 'failed']);
                
                return redirect()->route('dashboard.newsletter')
                    ->with('error', 'An error occurred while sending the newsletter: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send a scheduled newsletter immediately
     */
    public function sendNow(Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            abort(403, 'Unauthorized access to newsletter');
        }

        if ($newsletter->status !== 'scheduled') {
            return redirect()->back()->with('error', 'This newsletter is not scheduled.');
        }

        try {
            $newsletter->update([
                'status' => 'sending',
                'sent_at' => now(),
                'scheduled_at' => null
            ]);

            $result = $this->sendNewsletterSync($newsletter);
            
            if ($result) {
                $newsletter->update(['status' => 'sent']);
                return redirect()->back()->with('success', 'Newsletter sent successfully!');
            } else {
                $newsletter->update(['status' => 'failed']);
                return redirect()->back()->with('error', 'Failed to send newsletter.');
            }
        } catch (\Exception $e) {
            Log::error('Error sending newsletter immediately', [
                'newsletter_id' => $newsletter->id,
                'error' => $e->getMessage()
            ]);
            
            $newsletter->update(['status' => 'failed']);
            return redirect()->back()->with('error', 'An error occurred while sending the newsletter.');
        }
    }

    // Get subscribers based on newsletter recipient type
    private function getSubscribers($newsletter)
    {
        switch ($newsletter->recipient_type) {
            case 'all_followers':
                // Get all followers of this author
                return UserFollower::with('follower')
                    ->where('following_id', $newsletter->author_id)
                    ->get()
                    ->map(function($follower) {
                        return (object) [
                            'email' => $follower->follower->email,
                            'name' => $follower->follower->name
                        ];
                    });
            
            case 'selected_followers':
                // Get only selected followers - using selected_subscribers column
                $selectedIds = json_decode($newsletter->selected_subscribers, true) ?? [];
                if (empty($selectedIds)) {
                    return collect();
                }
                
                return UserFollower::with('follower')
                    ->whereIn('id', $selectedIds)
                    ->where('following_id', $newsletter->author_id)
                    ->get()
                    ->map(function($follower) {
                        return (object) [
                            'email' => $follower->follower->email,
                            'name' => $follower->follower->name
                        ];
                    });
            
            case 'test':
                // Send only to admin/author for testing
                $author = $newsletter->author->user ?? null;
                if ($author) {
                    return collect([(object) [
                        'email' => $author->email,
                        'name' => $author->name
                    ]]);
                }
                return collect();
            
            default:
                return collect();
        }
    }

    /**
     * Send newsletter synchronously (without queue)
     */
    public function sendNewsletterSync($newsletter)
    {
        try {
            $subscribers = $this->getSubscribers($newsletter);
            
            if ($subscribers->isEmpty()) {
                Log::warning('No subscribers found for newsletter', ['newsletter_id' => $newsletter->id]);
                return false;
            }

            $successCount = 0;
            $failCount = 0;

            foreach ($subscribers as $subscriber) {
                try {
                    Mail::send('mail.newsletter-email', [
                        'newsletter' => $newsletter,
                        'author' => $newsletter->author
                    ], function($message) use ($subscriber, $newsletter) {
                        $message->to($subscriber->email, $subscriber->name)
                               ->subject($newsletter->title)
                               ->from(config('mail.from.address'), config('mail.from.name'));
                    });
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to send newsletter to subscriber', [
                        'newsletter_id' => $newsletter->id,
                        'subscriber_email' => $subscriber->email,
                        'error' => $e->getMessage()
                    ]);
                    $failCount++;
                }
            }

            // Update newsletter status
            $newsletter->update([
                'status' => 'sent',
                'sent_at' => now(),
                'recipients_count' => $successCount + $failCount,
                'success_count' => $successCount,
                'failed_count' => $failCount
            ]);

            // Send success notification to author
            try {
                $author = $newsletter->author->user;
                if ($author) {
                    Mail::send('mail.newsletter-sent-successfully', [
                        'newsletter' => $newsletter,
                        'author' => $newsletter->author,
                        'stats' => [
                            'total_recipients' => $successCount + $failCount,
                            'sent_successfully' => $successCount,
                            'failed_deliveries' => $failCount
                        ]
                    ], function($message) use ($author, $newsletter) {
                        $message->to($author->email, $author->name)
                               ->subject('Newsletter Sent Successfully - ' . $newsletter->title)
                               ->from(config('mail.from.address'), config('mail.from.name'));
                    });
                }
            } catch (\Exception $e) {
                Log::error('Failed to send success notification', [
                    'newsletter_id' => $newsletter->id,
                    'error' => $e->getMessage()
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Newsletter sending failed', [
                'newsletter_id' => $newsletter->id,
                'error' => $e->getMessage()
            ]);
            
            $newsletter->update([
                'status' => 'failed',
                'sent_at' => now()
            ]);
            
            return false;
        }
    }

    /**
     * Manual send newsletter (synchronous)
     */
    public function send(Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            Log::warning('Unauthorized access attempt to send newsletter.', [
                'newsletter_id' => $newsletter->id,
                'newsletter_author_id' => $newsletter->author_id,
                'current_user_profile_id' => $currentProfileId,
                'auth_user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized access to newsletter');
        }

        // Send the newsletter synchronously
        $success = $this->sendNewsletterSync($newsletter);
        
        if ($success) {
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been sent successfully!');
        } else {
            return redirect()->route('dashboard.newsletter')
                ->with('error', 'Failed to send newsletter. Please check logs.');
        }
    }

    /**
     * Test send newsletter (send to author only)
     */
    public function testSend(Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            Log::warning('Unauthorized access attempt to test send newsletter.', [
                'newsletter_id' => $newsletter->id,
                'newsletter_author_id' => $newsletter->author_id,
                'current_user_profile_id' => $currentProfileId,
                'auth_user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized access to newsletter');
        }

        try {
            $author = $newsletter->author->user;
            
            if (!$author) {
                return redirect()->back()
                    ->with('error', 'Author information not found.');
            }

            // Send test email to author
            Mail::to($author->email, $author->name)
                ->send(new NewsletterEmail($newsletter, $author));
            
            return redirect()->back()
                ->with('success', 'Test newsletter sent to your email!');
                
        } catch (\Exception $e) {
            Log::error('Test newsletter sending failed', [
                'newsletter_id' => $newsletter->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to send test newsletter: ' . $e->getMessage());
        }
    }

    public function newsletter(Request $request){
        $profile_id = $this->getCurrentUser();
        
        // Get all newsletters for this author
        $allNewsletters = Newsletter::with('author', 'category')
            ->where('author_id', $profile_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'all_page');

        // Get draft newsletters
        $draftNewsletters = Newsletter::with('author', 'category')
            ->where('author_id', $profile_id)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'draft_page');

        // Get scheduled newsletters
        $scheduledNewsletters = Newsletter::with('author', 'category')
            ->where('author_id', $profile_id)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10, ['*'], 'scheduled_page');

        // Get sent newsletters
        $sentNewsletters = Newsletter::with('author', 'category')
            ->where('author_id', $profile_id)
            ->where('status', 'sent')
            ->orderBy('sent_at', 'desc')
            ->paginate(10, ['*'], 'sent_page');

        if ($request) {
            $allNewsletters->appends($request->query());
            $draftNewsletters->appends($request->query());
            $scheduledNewsletters->appends($request->query());
            $sentNewsletters->appends($request->query());
        }
            
        return view('dashboard.newsletter.newsletter', compact(
            'allNewsletters',
            'draftNewsletters', 
            'scheduledNewsletters',
            'sentNewsletters'
        ));
    }
    
    public function show(Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {

            Log::warning('Unauthorized access attempt to newsletter show page.', [
                'newsletter_id' => $newsletter->id,
                'newsletter_author_id' => $newsletter->author_id,
                'current_user_profile_id' => $currentProfileId,
                'auth_user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized access to newsletter');
        }
        
        return view('dashboard.newsletter.show', compact('newsletter'));
    }

    /**
     * Show the form for editing the specified newsletter.
     */
    public function edit(Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            Log::warning('Unauthorized access attempt to edit newsletter.', [
                'newsletter_id' => $newsletter->id,
                'newsletter_author_id' => $newsletter->author_id,
                'current_user_profile_id' => $currentProfileId,
                'auth_user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized access to newsletter');
        }
        
        // Only allow editing of draft and scheduled newsletters
        if (!in_array($newsletter->status, ['draft', 'scheduled'])) {
            return redirect()->route('newsletter.show', $newsletter->id)
                             ->with('error', 'Only draft or scheduled newsletters can be edited.');
        }
        
        $categories = Categorie::orderBy('name')->get();
        return view('dashboard.newsletter.edit', compact('newsletter', 'categories'));
    }

    /**
     * Update the specified newsletter in storage.
     */
    public function update(Request $request, Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            Log::warning('Unauthorized access attempt to update newsletter.', [
                'newsletter_id' => $newsletter->id,
                'newsletter_author_id' => $newsletter->author_id,
                'current_user_profile_id' => $currentProfileId,
                'auth_user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized access to newsletter');
        }
        
        // Only allow editing of draft and scheduled newsletters
        if (!in_array($newsletter->status, ['draft', 'scheduled'])) {
            return redirect()->route('newsletter.show', $newsletter->id)
                             ->with('error', 'Only draft or scheduled newsletters can be edited.');
        }

        $request->validate([
            'title' => 'required|string|min:10|max:100',
            'summary' => 'required|string|min:30|max:300',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,category_id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_featured_image' => 'nullable|boolean',
        ]);

        // Handle featured image upload
        $imagePath = $newsletter->featured_image;
        
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($newsletter->featured_image) {
                Storage::disk('public')->delete($newsletter->featured_image);
            }
            
            $image = $request->file('featured_image');
            $filename = time() . Auth::id() . '_' . $image->getClientOriginalName();
            $imagePath = 'featured_images/' . $filename;
            $image->storeAs('public/featured_images', $filename);
            $imagePath = 'featured_images/' . $filename;

        } elseif ($request->has('remove_featured_image') && $request->remove_featured_image) {
            // Remove featured image
            if ($newsletter->featured_image) {
                Storage::disk('public')->delete($newsletter->featured_image);
            }
            $imagePath = null;
        }

        // Update newsletter
        $newsletter->update([
            'title' => $request->title,
            'content' => $request->content,
            'summary' => $request->summary,
            'category_id' => $request->category_id,
            'featured_image' => $imagePath,
        ]);

        return redirect()->route('newsletter.show', $newsletter->id)
            ->with('success', 'Newsletter has been updated successfully!');
    }

    /**
     * Remove the specified newsletter from storage.
     */
    public function destroy(Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            Log::warning('Unauthorized access attempt to delete newsletter.', [
                'newsletter_id' => $newsletter->id,
                'newsletter_author_id' => $newsletter->author_id,
                'current_user_profile_id' => $currentProfileId,
                'auth_user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized access to newsletter');
        }
        
        // Only allow deletion of draft and scheduled newsletters
        if (!in_array($newsletter->status, ['draft', 'scheduled'])) {
            return redirect()->route('newsletter.show', $newsletter->id)
                ->with('error', 'Only draft and scheduled newsletters can be deleted. Sent newsletters cannot be deleted.');
        }

        // Delete featured image if exists
        if ($newsletter->featured_image) {
            Storage::disk('public')->delete($newsletter->featured_image);
        }

        $newsletter->delete();

        return redirect()->route('dashboard.newsletter')
            ->with('success', 'Newsletter has been deleted successfully!');
    }

    /**
     * Show subscribers page
     */
    public function subscribers(Request $request)
    {
        $profile_id = $this->getCurrentUser();
        // Use UserFollower instead of Subscriber
        $query = UserFollower::with(['follower.userProfile'])
            ->where('following_id', $profile_id);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('follower', function($userQuery) use ($search) {
                $userQuery->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->whereHas('follower', function($q) {
                    $q->orderBy('name', 'asc');
                });
                break;
            case 'name_desc':
                $query->whereHas('follower', function($q) {
                    $q->orderBy('name', 'desc');
                });
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $followers = $query->paginate(15);
        // Calculate statistics
        $totalSubscribers = UserFollower::where('following_id', $profile_id)->count();
        $totalSubscribers = UserFollower::where('following_id', $profile_id)->count();
        $newThisMonth = UserFollower::where('following_id', $profile_id)
            ->whereMonth('created_at', now()->month)
            ->count();
        $activeSubscribers = $totalSubscribers; // All followers are considered active
        
        $engagementRate = $totalSubscribers > 0 ? ($activeSubscribers / $totalSubscribers) * 100 : 0;
        
        return view('dashboard.subscribers', [
            'subscribers' => $followers, // Rename for consistency with view
            'totalSubscribers' => $totalSubscribers,
            'newThisMonth' => $newThisMonth,
            'activeSubscribers' => $activeSubscribers,
            'engagementRate' => $engagementRate
        ]);
    }

    /**
     * Remove a follower
     */
    public function removeSubscriber($id)
    {
        $profile_id = $this->getCurrentUser();
        
        // Find the follower relationship
        $followerRelation = UserFollower::where('id', $id)
            ->where('following_id', $profile_id)
            ->firstOrFail();
        
        $followerRelation->delete();
        
        return redirect()->route('dashboard.subscribers')
            ->with('success', 'Follower has been removed successfully.');
    }

    /**
     * Bulk remove subscribers
     */
    public function bulkRemoveSubscribers(Request $request)
    {
        $request->validate([
            'subscriber_ids' => 'required|array',
            'subscriber_ids.*' => 'exists:subscribers,id'
        ]);
        
        $profile_id = $this->getCurrentUser();
        
        $count = Subscriber::whereIn('id', $request->subscriber_ids)
            ->where('author_id', $profile_id)
            ->delete();
        
        return redirect()->route('dashboard.subscribers')
            ->with('success', "{$count} subscribers have been removed successfully.");
    }

    /**
     * Export subscribers
     */
    public function exportSubscribers(Request $request)
    {
        $profile_id = $this->getCurrentUser();
        
        $query = Subscriber::with(['user.userProfile'])
            ->where('author_id', $profile_id);
        
        // If specific IDs are provided
        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }
        
        $subscribers = $query->get();
        
        $format = $request->get('format', 'csv');
        
        if ($format === 'csv') {
            return $this->exportToCSV($subscribers);
        }
        
        return $this->exportToExcel($subscribers);
    }

    /**
     * Export subscribers to CSV
     */
    private function exportToCSV($subscribers)
    {
        $filename = 'subscribers_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Name',
                'Email',
                'Status',
                'Joined Date',
                'Last Activity',
                'User Type'
            ]);
            
            // CSV data
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->name ?? ($subscriber->user->name ?? 'Anonymous'),
                    $subscriber->email,
                    $subscriber->is_active ? 'Active' : 'Inactive',
                    $subscriber->created_at->format('Y-m-d H:i:s'),
                    $subscriber->last_activity_at ? $subscriber->last_activity_at->format('Y-m-d H:i:s') : 'Never',
                    $subscriber->user && $subscriber->user->userProfile ? 
                        ($subscriber->user->userProfile->title ?? 'Reader') : 'Guest'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export subscribers to Excel (placeholder - you can implement using Laravel Excel package)
     */
    private function exportToExcel($subscribers)
    {
        // For now, return CSV format
        return $this->exportToCSV($subscribers);
    }

    /**
     * Create newsletter from existing article
     */
    public function createFromArticle($article_id)
    {
        try {
            // Get the article
            $article = Article::with(['author.user', 'categorie'])->findOrFail($article_id);
            
            // Get current user profile
            $currentProfileId = $this->getCurrentUser();
            
            // Check if user is the author of the article
            if ($article->author_id !== $currentProfileId) {
                return redirect()->back()->with('error', 'You can only convert your own articles to newsletters.');
            }
            
            // Check if article is published
            if ($article->status !== 'published') {
                return redirect()->back()->with('error', 'Only published articles can be converted to newsletters.');
            }
            
            // Create newsletter from article
            $newsletter = Newsletter::create([
                'author_id' => $currentProfileId,
                'title' => $article->title,
                'content' => $this->formatArticleForNewsletter($article),
                'summary' => $this->createSummary($article),
                'category_id' => $article->category_id,
                'featured_image' => $article->featured_image_url,
                'status' => 'draft',
                'recipient_type' => null,
                'selected_subscribers' => null,
                'scheduled_at' => null,
                'sent_at' => null,
                'total_sent' => 0,
                'total_failed' => 0,
            ]);
            
            return redirect()->route('newsletter.send-options', $newsletter->id)
                            ->with('success', 'Article converted to newsletter! Configure your send options.');
                            
        } catch (\Exception $e) {
            Log::error('Failed to convert article to newsletter', [
                'article_id' => $article_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to convert article. Please try again.');
        }
    }

    private function formatArticleForNewsletter($article)
    {
        $content = '<div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">';
        
        // Add title
        $content .= '<h1 style="color: #333; font-size: 24px; margin-bottom: 10px;">' . $article->title . '</h1>';
        
        // Add meta info
        $content .= '<p style="color: #666; font-size: 14px; margin-bottom: 20px;">';
        $content .= 'Published on ' . $article->created_at->format('F j, Y');
        if ($article->categorie) {
            $content .= ' • ' . $article->categorie->name;
        }
        $content .= '</p>';
        
        // Add featured image if exists
        if ($article->featured_image_url) {
            $content .= '<img src="' . asset('storage/' . $article->featured_image_url) . '" ';
            $content .= 'alt="' . $article->title . '" ';
            $content .= 'style="width: 100%; height: auto; margin: 20px 0; border-radius: 8px;">';
        }
        
        // Add main content
        $content .= '<div style="line-height: 1.6; color: #444;">' . $article->content . '</div>';
        
        // Add footer
        $content .= '<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">';
        $content .= '<p style="color: #666; font-size: 14px;">Read online: ';
        $content .= '<a href="' . route('articles.show', $article->article_id) . '">View Article</a></p>';
        $content .= '</div>';
        
        $content .= '</div>';
        
        return $content;
    }

    private function createSummary($article)
    {
        $plainText = strip_tags($article->content);
        $plainText = preg_replace('/\s+/', ' ', trim($plainText));
        
        if (strlen($plainText) > 200) {
            return substr($plainText, 0, 197) . '...';
        }
        
        return $plainText ?: 'Newsletter from article: ' . $article->title;
    }

    /**
     * Get recent activity for the author's profile - WITHOUT comments
     */
    public function getRecentActivity($profileId, $limit = 10)
    {
        $activities = collect();
        
        // Get recent newsletters
        $newsletters = Newsletter::where('author_id', $profileId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($newsletter) {
                return [
                    'type' => 'newsletter_created',
                    'title' => 'Created newsletter: ' . $newsletter->title,
                    'description' => 'Published a new newsletter',
                    'date' => $newsletter->created_at,
                    'icon' => 'fas fa-newspaper',
                    'color' => 'primary',
                    'link' => route('newsletter.show', $newsletter->id),
                    'item' => $newsletter
                ];
            });
        
        // Get recent articles (if you have articles)
        if (class_exists('App\Models\Article')) {
            $articles = Article::where('author_id', $profileId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($article) {
                    return [
                        'type' => 'article_created',
                        'title' => 'Published article: ' . $article->title,
                        'description' => 'Published a new article',
                        'date' => $article->created_at,
                        'icon' => 'fas fa-file-alt',
                        'color' => 'success',
                        'link' => route('articles.show', $article->article_id),
                        'item' => $article
                    ];
                });
            $activities = $activities->merge($articles);
        }
        
        // Get recent followers
        $followers = UserFollower::where('following_id', $profileId)
            ->with('follower')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($follower) {
                return [
                    'type' => 'new_follower',
                    'title' => $follower->follower->name . ' started following you',
                    'description' => 'You have a new follower',
                    'date' => $follower->created_at,
                    'icon' => 'fas fa-user-plus',
                    'color' => 'warning',
                    'link' => '#',
                    'item' => $follower,
                    'user' => $follower->follower
                ];
            });
        
        // Merge activities (without comments)
        $activities = $activities->merge($newsletters)
                               ->merge($followers);
        
        // Sort by date and limit
        return $activities->sortByDesc('date')->take($limit)->values();
    }

    /**
     * Update your existing method that shows the dashboard or profile
     */
    public function dashboard()
    {
        $profileId = $this->getCurrentUser();
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity($profileId, 15);
        
        // Get other dashboard data
        $totalNewsletters = Newsletter::where('author_id', $profileId)->count();
        $totalFollowers = UserFollower::where('following_id', $profileId)->count();
        $totalSent = Newsletter::where('author_id', $profileId)
                              ->where('status', 'sent')
                              ->sum('success_count');
        
        // Get recent newsletters
        $recentNewsletters = Newsletter::where('author_id', $profileId)
                                      ->orderBy('created_at', 'desc')
                                      ->limit(5)
                                      ->get();
        
        return view('dashboard.index', compact(
            'totalNewsletters',
            'totalFollowers', 
            'totalSent',
            'recentNewsletters',
            'recentActivity'
        ));
    }

    public function processNewsletter(Request $request, $newsletter)
    {
        try {
            // Get the newsletter
            $newsletter = Newsletter::findOrFail($newsletter);
            
            // Instead of setting status to 'sending', keep it as 'scheduled' during processing
            // and only update to 'sent' after successful completion
            
            // Validate request
            $request->validate([
                'action_type' => 'required|in:send,schedule,draft',
                'recipient_type' => 'required_if:action_type,send|in:all_followers,selected_followers,test',
                'selected_followers' => 'required_if:recipient_type,selected_followers|array',
                'scheduled_at' => 'required_if:action_type,schedule|date|after:now'
            ]);

            if ($request->action_type === 'draft') {
                // Save as draft
                $newsletter->update(['status' => 'draft']);
                return redirect()->route('dashboard.newsletter')->with('success', 'Newsletter saved as draft.');
            }

            if ($request->action_type === 'schedule') {
                // Schedule newsletter
                $newsletter->update([
                    'status' => 'scheduled',
                    'scheduled_at' => $request->scheduled_at,
                    'selected_subscribers' => $request->recipient_type === 'selected_followers' ? $request->selected_followers : null
                ]);
                return redirect()->route('dashboard.newsletter')->with('success', 'Newsletter scheduled successfully.');
            }

            // Send newsletter immediately
            // Keep status as current until sending is complete
            $currentStatus = $newsletter->status;
            
            // Get recipients
            $recipients = $this->getRecipients($newsletter, $request->recipient_type, $request->selected_followers ?? []);
            
            if (empty($recipients)) {
                return redirect()->back()->with('error', 'No recipients found to send the newsletter to.');
            }

            // Send emails
            $sentCount = 0;
            $failedCount = 0;
            
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new NewsletterEmail($newsletter, $newsletter->author));
                    $sentCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Failed to send newsletter to ' . $recipient->email . ': ' . $e->getMessage());
                }
            }
            
            // Only update status to 'sent' after all emails are processed
            $newsletter->update([
                'status' => 'sent',
                'sent_at' => now(),
                'total_sent' => $sentCount,
                'total_failed' => $failedCount
            ]);
            
            $message = "Newsletter sent successfully! Sent to {$sentCount} recipients.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} emails failed to send.";
            }
            
            return redirect()->route('dashboard.newsletter')->with('success', $message);
            
        } catch (\Exception $e) {
            // If there was an error, revert status if it was changed
            if (isset($currentStatus) && $newsletter->status !== $currentStatus) {
                $newsletter->update(['status' => $currentStatus]);
            }
            
            Log::error('Newsletter sending error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while sending the newsletter: ' . $e->getMessage());
        }
    }

    private function getRecipients($newsletter, $recipientType, $selectedFollowers = [])
    {
        $authorId = $newsletter->author_id;
        
        switch ($recipientType) {
            case 'all_followers':
                return User::whereHas('following', function($query) use ($authorId) {
                    $query->where('followed_user_id', $authorId);
                })->get();
                
            case 'selected_followers':
                return User::whereHas('following', function($query) use ($authorId) {
                    $query->where('followed_user_id', $authorId);
                })->whereIn('id', $selectedFollowers)->get();
                
            case 'test':
                return collect([Auth::user()]);
                
            default:
                return collect([]);
        }
    }
}