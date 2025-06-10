<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $newsletter;
    public $subscriber;
    public $unsubscribeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($newsletter, $subscriber = null)
    {
        $this->newsletter = $newsletter;
        $this->subscriber = $subscriber;
        
        // // Generate unsubscribe URL if subscriber is provided
        // if ($subscriber) {
        //     $this->unsubscribeUrl = route('newsletter.unsubscribe', [
        //         'token' => $subscriber->unsubscribe_token ?? 'demo-token'
        //     ]);
        // } else {
        //     $this->unsubscribeUrl = '#';
        // }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter['title'] ?? 'Newsletter',
            from: config('mail.from.address', 'newsletter@example.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.newsletters', // Changed from 'emails.newsletter' to 'mail.newsletters'
            with: [
                'newsletter' => $this->newsletter,
                'subscriber' => $this->subscriber,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'currentYear' => date('Y'),
                // 'preferencesUrl' => route('newsletter.preferences', [
                //     'token' => $this->subscriber->preferences_token ?? 'demo-token'
                // ]) ?? '#'
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}