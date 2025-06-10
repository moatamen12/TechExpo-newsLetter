<!DOCTYPE html>
<html>
<head>
    <title>Queue Monitor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Queue Monitor</h1>
    
    <div class="status {{ $pending > 0 ? 'warning' : 'success' }}">
        <h3>Queue Status</h3>
        <p><strong>Pending Jobs:</strong> {{ $pending }}</p>
        <p><strong>Processing Jobs:</strong> {{ $processing }}</p>
        <p><strong>Failed Jobs:</strong> {{ $failed }}</p>
    </div>
    
    @if($pending == 0 && $processing == 0)
        <div class="status success">
            <p>✅ Queue is empty - all jobs processed!</p>
        </div>
    @else
        <div class="status warning">
            <p>⏳ Jobs are pending or processing...</p>
            <p><em>Make sure queue worker is running: <code>php artisan queue:work</code></em></p>
        </div>
    @endif
    
    @if($recentJobs->count() > 0)
        <h3>Recent Jobs</h3>
        <table>
            <thead>
                <tr>
                    <th>Queue</th>
                    <th>Job Type</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentJobs as $job)
                    <tr>
                        <td>{{ $job->queue ?? 'default' }}</td>
                        <td>{{ class_basename(json_decode($job->payload)->data->commandName ?? 'Unknown') }}</td>
                        <td>{{ $job->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    @if($failedJobs->count() > 0)
        <h3>Failed Jobs</h3>
        <div class="status error">
            <table>
                <thead>
                    <tr>
                        <th>Queue</th>
                        <th>Error</th>
                        <th>Failed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($failedJobs as $job)
                        <tr>
                            <td>{{ $job->queue }}</td>
                            <td>{{ Str::limit($job->exception, 100) }}</td>
                            <td>{{ $job->failed_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <div style="margin-top: 30px;">
        <a href="/test-queue" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Test Queue</a>
        <a href="/queue-monitor" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">Refresh</a>
    </div>
</body>
</html>