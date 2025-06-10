<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterEmail;
use App\Mail\NewsletterSentSuccessfully;
use App\Models\Newsletter;
use App\Models\Subscriber;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $newsletter;
    public $tries = 3;  // Retry failed jobs 3 times
    public $timeout = 300;  // 5 minute timeout

    /**
     * Create a new job instance.
     */
    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
        $this->onQueue('newsletters');  // Use dedicated queue
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update status to 'sending'
            $this->newsletter->update(['status' => 'sending']);
            
            // Get author information
            $author = $this->newsletter->author->user ?? null;
            
            if (!$author) {
                Log::error('Newsletter author not found', ['newsletter_id' => $this->newsletter->id]);
                return;
            }

            // Get subscribers
            $subscribers = $this->getSubscribers($this->newsletter);
            
            if ($subscribers->isEmpty()) {
                Log::warning('No subscribers found for newsletter', ['newsletter_id' => $this->newsletter->id]);
                return;
            }

            $stats = [
                'total_recipients' => $subscribers->count(),
                'sent_successfully' => 0,
                'failed_deliveries' => 0
            ];

            // Send newsletter to each subscriber
            foreach ($subscribers as $subscriber) {
                try {
                    Mail::to($subscriber->email, $subscriber->name ?? 'Subscriber')
                        ->send(new NewsletterEmail($this->newsletter, $author));
                    
                    $stats['sent_successfully']++;
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send newsletter to subscriber', [
                        'newsletter_id' => $this->newsletter->id,
                        'subscriber_email' => $subscriber->email,
                        'error' => $e->getMessage()
                    ]);
                    
                    $stats['failed_deliveries']++;
                }
            }

            // Update newsletter status
            $this->newsletter->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            // Send success notification to author
            try {
                Mail::to($author->email, $author->name)
                    ->send(new NewsletterSentSuccessfully($this->newsletter, $author, $stats));
                    
                Log::info('Newsletter sent successfully', [
                    'newsletter_id' => $this->newsletter->id,
                    'stats' => $stats
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send success notification', [
                    'newsletter_id' => $this->newsletter->id,
                    'author_email' => $author->email,
                    'error' => $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Newsletter sending job failed', [
                'newsletter_id' => $this->newsletter->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Newsletter sending job failed permanently', [
            'newsletter_id' => $this->newsletter->id,
            'error' => $exception->getMessage()
        ]);

        // Update newsletter status to failed
        $this->newsletter->update([
            'status' => 'failed'
        ]);
    }

    /**
     * Get subscribers based on newsletter recipient type
     */
    private function getSubscribers($newsletter)
    {
        switch ($newsletter->recipient_type) {
            case 'all':
                return Subscriber::all();
            
            case 'category':
                return Subscriber::where('author_id', $newsletter->author_id)->get();
            
            case 'test':
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
}