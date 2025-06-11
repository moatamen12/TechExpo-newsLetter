<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\UserProfiles;
use App\Models\Categorie;
use App\Models\Subscriber;
use App\Models\UserFollower;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\NewsletterEmail;
use App\Mail\NewsletterSentSuccessfully;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
            'content' => 'required|string', // Assuming content comes from editor or template logic handled elsewhere if 'content_method' is used
            'category_id' => 'required|exists:categories,category_id',
            'send_option' => 'required|in:now,scheduled,draft',
            'scheduled_at' => 'required_if:send_option,scheduled|nullable|date|after:now',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // Add validation for content_method, html_template, edit_template if they are part of this form
        ]);

        $profile_id = $this->getCurrentUser(); // Use corrected method name

        // Handle featured image upload
        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . Auth::id() . '_' . $image->getClientOriginalName();
            $imagePath = 'featured_images/' . $filename; // Relative path for storage
            $image->storeAs('public/featured_images', $filename); // Store in storage/app/public/featured_images
            $imagePath = 'featured_images/' . $filename; // Path to be stored in DB
        }

        // Determine newsletter status
        $status = 'draft';
        $scheduled_at = null;
        $sent_at = null; // Initialize sent_at

        if ($request->send_option === 'scheduled') {
            $status = 'scheduled';
            $scheduled_at = $request->scheduled_at;
        } elseif ($request->send_option === 'draft') {
            $status = 'draft';
        }
        // 'now' option will be handled by redirecting to sendOptions

        // Create newsletter
        $newsletter = Newsletter::create([
            'title' => $request->title,
            'content' => $request->content, // Ensure content is correctly sourced
            'summary' => $request->summary,
            'author_id' => $profile_id,
            'category_id' => $request->category_id,
            'featured_image' => $imagePath,
            'status' => $status,
            'scheduled_at' => $scheduled_at,
            'sent_at' => $sent_at,
            // Add recipient_type if it's determined at this stage, otherwise it's set in sendOptions/sendConfirm
        ]);

        // Handle different send options
        if ($request->send_option === 'now') {
            // Redirect to send options page
            return redirect()->route('newsletter.send-options', $newsletter->id)
                             ->with('success', 'Newsletter draft created. Please configure send options.');
        } elseif ($request->send_option === 'scheduled') {
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been scheduled successfully!');
        } else { // draft
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been saved as a draft.');
        }
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

        // Get subscriber counts for display
        $allSubscribers = UserFollower::count(); // Count all users who can be followed
        $categorySubscribers = UserFollower::where('following_id', $newsletter->author_id)->count(); // Followers of the author
        
        return view('dashboard.newsletter.send-options', compact('newsletter', 'allSubscribers', 'categorySubscribers'));
    }

    /**
     * Confirm and send newsletter
     */
    public function sendConfirm(Request $request, Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();
        if ($newsletter->author_id !== $currentProfileId) {
            Log::warning('Unauthorized access attempt to confirm send newsletter.', [
                'newsletter_id' => $newsletter->id,
                'newsletter_author_id' => $newsletter->author_id,
                'current_user_profile_id' => $currentProfileId,
                'auth_user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized access to newsletter');
        }

        $request->validate([
            'recipient_type' => 'required|in:all,category,test',
            'send_time' => 'required|in:immediate,scheduled',
            'scheduled_at' => 'required_if:send_time,scheduled|nullable|date|after:now',
        ]);

        // Update newsletter with recipient type and timing
        $updateData = [
            'recipient_type' => $request->recipient_type,
            'status' => $request->send_time === 'scheduled' ? 'scheduled' : 'sent'
        ];

        if ($request->send_time === 'scheduled') {
            $updateData['scheduled_at'] = $request->scheduled_at;
        } else {
            $updateData['sent_at'] = now();
        }

        $newsletter->update($updateData);

        if ($request->send_time === 'immediate') {
            // Send immediately
            $this->sendNewsletterSync($newsletter); // Ensure this method exists and works
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been sent successfully!');
        } else {
            // Schedule for later
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been scheduled successfully!');
        }
    }

    /**
     * Send newsletter synchronously (without queue)
     */
    public function sendNewsletterSync($newsletter)
    {
        try {
            // Get author information
            $author = $newsletter->author->user ?? null;
            
            if (!$author) {
                Log::error('Newsletter author not found', ['newsletter_id' => $newsletter->id]);
                return false;
            }

            // Get subscribers
            $subscribers = $this->getSubscribers($newsletter);
            
            if ($subscribers->isEmpty()) {
                Log::warning('No subscribers found for newsletter', ['newsletter_id' => $newsletter->id]);
                return false;
            }

            // Update newsletter status to 'sending'
            $newsletter->update(['status' => 'sending']);

            $stats = [
                'total_recipients' => $subscribers->count(),
                'sent_successfully' => 0,
                'failed_deliveries' => 0
            ];

            // Send newsletter to each subscriber synchronously
            foreach ($subscribers as $subscriber) {
                try {
                    Mail::to($subscriber->email, $subscriber->name ?? 'Subscriber')
                        ->send(new NewsletterEmail($newsletter, $author));
                    
                    $stats['sent_successfully']++;
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send newsletter to subscriber', [
                        'newsletter_id' => $newsletter->id,
                        'subscriber_email' => $subscriber->email,
                        'error' => $e->getMessage()
                    ]);
                    
                    $stats['failed_deliveries']++;
                }
            }

            // Update newsletter status to 'sent'
            $newsletter->update([
                'status' => 'sent',
                'sent_at' => now(),
                'total_sent' => $stats['sent_successfully'],
                'total_failed' => $stats['failed_deliveries']
            ]);

            // Send success notification to author
            try {
                Mail::to($author->email, $author->name)
                    ->send(new NewsletterSentSuccessfully($newsletter, $author, $stats));
                    
                Log::info('Newsletter sent successfully', [
                    'newsletter_id' => $newsletter->id,
                    'stats' => $stats
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send success notification', [
                    'newsletter_id' => $newsletter->id,
                    'author_email' => $author->email,
                    'error' => $e->getMessage()
                ]);
            }

            return true;
            
        } catch (\Exception $e) {
            Log::error('Newsletter sending failed', [
                'newsletter_id' => $newsletter->id,
                'error' => $e->getMessage()
            ]);
            
            // Update newsletter status to failed
            $newsletter->update(['status' => 'failed']);
            
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
        $success = $this->sendNewsletterSync($newsletter); // Ensure this method exists
        
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

    // Get subscribers based on newsletter recipient type
    private function getSubscribers($newsletter)
    {
        switch ($newsletter->recipient_type) {
            case 'all':
                return Subscriber::all();
            
            case 'category':
                // Get subscribers interested in this category/author
                return Subscriber::where('author_id', $newsletter->author_id)->get();
            
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

    public function newsletter(Request $request){
        $profile_id = $this->getCurrentUser();
        
        $newsletters = Newsletter::with('author')
            ->where('author_id', $profile_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request) {
            $newsletters->appends($request->query());
        }
            
        return view('dashboard.newsletter.newsletter', compact('newsletters'));
    }
    
    public function show(Newsletter $newsletter)
    {
        $currentProfileId = $this->getCurrentUser();

        dd($newsletter->id);
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
            $imagePath = 'featured_images/' . $filename; // Relative path for storage
            $image->storeAs('public/featured_images', $filename); // Store in storage/app/public/featured_images
            $imagePath = 'featured_images/' . $filename; // Path to be stored in DB

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
}