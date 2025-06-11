<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterEmail extends Mailable
{
    public $newsletter;
    public $author;

    /**
     * Create a new message instance.
     */
    public function __construct($newsletter, $author)
    {
        $this->newsletter = $newsletter;
        $this->author = $author;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter->title ?? 'TechExpo Newsletter',
        );
    }

    /**
     * Get the message content definition.
    */
    public function content(): Content
    {
        return new Content(
            view: 'mail.newsletter-email',
            with: [
                'newsletter' => $this->newsletter,
                'author' => $this->author,
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