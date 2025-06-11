<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterSentSuccessfully extends Mailable
{
    public $newsletter;
    public $author;
    public $stats;

    /**
     * Create a new message instance.
     */
    public function __construct($newsletter, $author, $stats)
    {
        $this->newsletter = $newsletter;
        $this->author = $author;
        $this->stats = $stats;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Newsletter Sent Successfully - ' . ($this->newsletter->title ?? 'TechExpo Newsletter'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.newsletter-sent-succesfuly',
            with: [
                'newsletter' => $this->newsletter,
                'author' => $this->author,
                'stats' => $this->stats,
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