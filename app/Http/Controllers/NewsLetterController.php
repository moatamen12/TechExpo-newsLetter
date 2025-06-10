<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\UserProfiles;
use App\Models\Categorie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\NewsletterEmail;
use Illuminate\Support\Facades\Mail;

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
            'status' => 'required|in:draft,published'
        ]);

        $profile_id = $this->getCorrentUser();

        // Handle featured image upload
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('newsletter-images', 'public');
        }

        // Determine newsletter status based on send option and form submission
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
            'featured_image' => $featuredImagePath,
            'newsletter_type' => $request->newsletter_type,
            'recipient_type' => $request->recipient_type,
            'status' => $status,
            'scheduled_at' => $scheduled_at,
            'sent_at' => $sent_at,
        ]);

        // Handle immediate sending
        if ($status === 'sent') {
            // Here you would typically queue the newsletter for sending
            // For now, we'll just flash a success message
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been sent successfully!');
        }

        // Handle scheduled sending
        if ($status === 'scheduled') {
            return redirect()->route('dashboard.newsletter')
                ->with('success', 'Newsletter has been scheduled successfully!');
        }

        // Handle draft
        return redirect()->route('dashboard.newsletter')
            ->with('success', 'Newsletter has been saved as draft!');
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
    
    public function show($id)
    {
        $newsletter = Newsletter::with('author')->findOrFail($id);
        return view('dashboard.newsletter.show', compact('newsletter'));
    }
    
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

// public function sendNewsletter(Request $request)
// {
//     // Validate and process form data
//     $newsletterData = [
//         'title' => $request->input('title'),
//         'summary' => $request->input('summary'),
//         'content' => $request->input('content'),
//         'newsletter_type' => $request->input('newsletter_type'),
//         'featured_image' => $request->hasFile('featured_image') 
//             ? $request->file('featured_image')->store('newsletter-images', 'public') 
//             : null,
//     ];

//     // Get subscribers (example)
//     $subscribers = \App\Models\Subscriber::where('is_active', true)->get();

//     // Send emails
//     foreach ($subscribers as $subscriber) {
//         Mail::to($subscriber->email)->send(
//             new NewsletterEmail($newsletterData, $subscriber)
//         );
//     }

//     return redirect()->back()->with('success', 'Newsletter sent successfully!');
// }
}