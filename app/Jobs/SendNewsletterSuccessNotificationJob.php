<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterSentSuccessfully;

class SendNewsletterSuccessNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $newsletter;
    public $author;
    public $totalRecipients;

    public function __construct($newsletter, $author, $totalRecipients)
    {
        $this->newsletter = $newsletter;
        $this->author = $author;
        $this->totalRecipients = $totalRecipients;
    }

    public function handle(): void
    {
        $stats = [
            'total_recipients' => $this->totalRecipients,
            'sent_successfully' => $this->totalRecipients, // Assume success for now
            'failed_deliveries' => 0
        ];

        Mail::to($this->author->email, $this->author->name)
            ->send(new NewsletterSentSuccessfully($this->newsletter, $this->author, $stats));
    }
}