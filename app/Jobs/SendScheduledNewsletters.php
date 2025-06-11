<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Http\Controllers\NewsLetterController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendScheduledNewsletters implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $scheduledNewsletters = Newsletter::where('status', 'scheduled')
            ->where('scheduled_at', '<=', Carbon::now())
            ->get();

        foreach ($scheduledNewsletters as $newsletter) {
            try {
                Log::info('Processing scheduled newsletter', ['id' => $newsletter->id]);
                
                // Update status to sending
                $newsletter->update(['status' => 'sending']);
                
                // Send the newsletter
                $controller = new NewsLetterController();
                $result = $controller->sendNewsletterSync($newsletter);
                
                if ($result) {
                    $newsletter->update([
                        'status' => 'sent',
                        'sent_at' => Carbon::now()
                    ]);
                    Log::info('Scheduled newsletter sent successfully', ['id' => $newsletter->id]);
                } else {
                    $newsletter->update(['status' => 'failed']);
                    Log::error('Failed to send scheduled newsletter', ['id' => $newsletter->id]);
                }
                
            } catch (\Exception $e) {
                Log::error('Error sending scheduled newsletter', [
                    'id' => $newsletter->id,
                    'error' => $e->getMessage()
                ]);
                $newsletter->update(['status' => 'failed']);
            }
        }
    }
}
