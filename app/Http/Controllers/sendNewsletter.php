<?php
// In your newsletter controller
use App\Mail\NewsletterEmail;
use Illuminate\Support\Facades\Mail;

public function sendNewsletter(Request $request)
{
    // Validate and process form data
    $newsletterData = [
        'title' => $request->input('title'),
        'summary' => $request->input('summary'),
        'content' => $request->input('content'),
        'newsletter_type' => $request->input('newsletter_type'),
        'featured_image' => $request->hasFile('featured_image') 
            ? $request->file('featured_image')->store('newsletter-images', 'public') 
            : null,
    ];

    // Get subscribers (example)
    $subscribers = \App\Models\Subscriber::where('is_active', true)->get();

    // Send emails
    foreach ($subscribers as $subscriber) {
        Mail::to($subscriber->email)->send(
            new NewsletterEmail($newsletterData, $subscriber)
        );
    }

    return redirect()->back()->with('success', 'Newsletter sent successfully!');
}