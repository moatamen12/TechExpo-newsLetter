<?php


namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewsletterEmail;

class SendNewsletterToSubscriberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $newsletter;
    public $subscriber;
    public $author;
    public $timeout = 60; // 1 minute timeout per email
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($newsletter, $subscriber, $author)
    {
        $this->newsletter = $newsletter;
        $this->subscriber = $subscriber;
        $this->author = $author;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->subscriber->email, $this->subscriber->name ?? 'Subscriber')
                ->send(new NewsletterEmail($this->newsletter, $this->author));

            Log::info('Newsletter sent to subscriber', [
                'newsletter_id' => $this->newsletter->id,
                'subscriber_email' => $this->subscriber->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send newsletter to subscriber', [
                'newsletter_id' => $this->newsletter->id,
                'subscriber_email' => $this->subscriber->email,
                'error' => $e->getMessage()
            ]);
            
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Newsletter sending to subscriber failed permanently', [
            'newsletter_id' => $this->newsletter->id,
            'subscriber_email' => $this->subscriber->email,
            'error' => $exception->getMessage()
        ]);
    }
}