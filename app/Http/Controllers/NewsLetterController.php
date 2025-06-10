<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\UserProfiles;
use App\Models\Categorie;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\NewsletterEmail;
use App\Mail\NewsletterSentSuccessfully;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendNewsletterJob;
use App\Jobs\SendNewsletterToSubscriberJob;
use App\Jobs\SendNewsletterSuccessNotificationJob;

class NewsLetterController extends Controller
{
    //get the current user profile id
    public function getCorrentUser():int
    {
        $user = Auth::id();
        $profile_id = UserProfiles::where('user_id', $user)->first()->profile_id;
        return $profile_id;
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
            'newsletter_type' => 'required|in:weekly,special,announcement',
            'category_id' => 'required|exists:categories,category_id',
            'send_option' => 'required|in:now,scheduled',
            'recipient_type' => 'required|in:all,category,test',
            'scheduled_at' => 'required_if:send_option,scheduled|nullable|date|after:now',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:draft,published,scheduled'
        ]);

        $profile_id = $this->getCorrentUser();

        // Handle featured image upload
        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . Auth::id() . '_' . $image->getClientOriginalName();
            $imagePath = 'featured_images/' . $filename;
            $image->storeAs('featured_images', $filename,'public');
        }

        // Determine newsletter status
        $status = 'draft';
        $scheduled_at = null;
        $sent_at = null;

        if ($request->status === 'published') {
            if ($request->send_option === 'now') {
                $status = 'sent';
                $sent_at = now();
            } elseif ($request->send_option === 'scheduled') {
                $status = 'scheduled';
                $scheduled_at = $request->scheduled_at;
            }
        }

        // Create newsletter
        $newsletter = Newsletter::create([
            'title' => $request->title,
            'content' => $request->content,
            'summary' => $request->summary,
            'author_id' => $profile_id,
            'category_id' => $request->category_id,
            'featured_image' => $imagePath,
            'newsletter_type' => $request->newsletter_type,
            'recipient_type' => $request->recipient_type,
            'status' => $status,
            'scheduled_at' => $scheduled_at,
            'sent_at' => $sent_at,
        ]);

        // Enhanced queue logic
        if ($status === 'sent') {
            // Use queue chains for better control
            SendNewsletterJob::dispatch($newsletter)
                ->onQueue('newsletters')  // Dedicated queue
                ->delay(now()->addSeconds(5));  // Small delay
        } elseif ($status === 'scheduled' && $scheduled_at) {
            // Schedule for later
            SendNewsletterJob::dispatch($newsletter)
                ->delay($scheduled_at);
        }

        return redirect()->route('dashboard.newsletter')
            ->with('success', 'Newsletter has been queued successfully!');
    }

    /**
     * Send newsletter using jobs (queued)
     */
    public function sendNewsletterQueued($newsletter)
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

            // Dispatch individual jobs for each subscriber
            foreach ($subscribers as $subscriber) {
                SendNewsletterToSubscriberJob::dispatch($newsletter, $subscriber, $author)
                    ->delay(now()->addSeconds(rand(1, 10))); // Add random delay to prevent overwhelming
            }

            // Schedule success notification job after all emails
            $delayMinutes = ceil($subscribers->count() / 10); // Estimate completion time
            SendNewsletterSuccessNotificationJob::dispatch($newsletter, $author, $subscribers->count())
                ->delay(now()->addMinutes($delayMinutes));

            Log::info('Newsletter queued for sending', [
                'newsletter_id' => $newsletter->id,
                'subscriber_count' => $subscribers->count()
            ]);

            return true;
            
        } catch (\Exception $e) {
            Log::error('Newsletter queueing failed', [
                'newsletter_id' => $newsletter->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Manual send newsletter (queued)
     */
    public function send($id)
    {
        $newsletter = Newsletter::with('author.user')->findOrFail($id);
        
        // Check if user owns this newsletter
        if ($newsletter->author_id !== $this->getCorrentUser()) {
            abort(403, 'Unauthorized access to newsletter');
        }

        // Dispatch the job
        SendNewsletterJob::dispatch($newsletter);
        
        return redirect()->route('dashboard.newsletter')
            ->with('success', 'Newsletter has been queued for sending!');
    }

    /**
     * Test send newsletter (send to author only)
     */
    public function testSend($id)
    {
        $newsletter = Newsletter::with('author.user')->findOrFail($id);
        
        // Check if user owns this newsletter
        if ($newsletter->author_id !== $this->getCorrentUser()) {
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
                // Get subscribers interested in this category
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
        $profile_id = $this->getCorrentUser();
        
        $newsletters = Newsletter::with('author')
            ->where('author_id', $profile_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request) {
            $newsletters->appends($request->query());
        }
            
        return view('dashboard.newsletter.newsletter', compact('newsletters'));
    }
    
    // public function show($id)
    // {
    //     $newsletter = Newsletter::with('author')->findOrFail($id);
    //     return view('dashboard.newsletter.show', compact('newsletter'));
    // }
    
    // Helper methods for filtering (similar to DashboardController)
    public function sentNewsletters($user_id, $limit = 10, Request $request = null)
    {   
        $newsletters = Newsletter::with('author')
            ->where('author_id', $user_id)
            ->where('status', 'sent')
            ->orderBy('sent_at', 'desc')
            ->paginate($limit);
        
        if ($request) {
            $newsletters->appends($request->query());
        }
        
        return $newsletters;
    }

    public function scheduledNewsletters($user_id, $limit = 10, Request $request = null)
    {   
        $newsletters = Newsletter::with('author')
            ->where('author_id', $user_id)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at', 'asc') // Show earliest scheduled first
            ->paginate($limit);
        
        if ($request) {
            $newsletters->appends($request->query());
        }
        
        return $newsletters;
    }

    public function draftNewsletters($user_id, $limit = 10, Request $request = null)
    {   
        $newsletters = Newsletter::with('author')
            ->where('author_id', $user_id)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
            
        if ($request) {
            $newsletters->appends($request->query());
        }
        
        return $newsletters;
    }
}