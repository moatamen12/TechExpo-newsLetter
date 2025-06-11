<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendScheduledNewsletters as SendScheduledNewslettersJob;

class SendScheduledNewsletters extends Command
{
    protected $signature = 'newsletters:send-scheduled';
    protected $description = 'Send scheduled newsletters';

    public function handle()
    {
        $this->info('Checking for scheduled newsletters...');
        
        SendScheduledNewslettersJob::dispatch();
        
        $this->info('Scheduled newsletter job dispatched.');
        return 0;
    }
}