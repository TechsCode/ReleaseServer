<?php

namespace App\Console\Commands;

use App\Models\UpdateRequest;
use Illuminate\Console\Command;

class DeleteOldUpdateRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:old-update-requests {--days-old=7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old update requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days_old = $this->option('days-old') ?? 7;

        $this->info("Deleting update requests older than {$days_old} days...");
        $update_requests = UpdateRequest::query()
            ->where('created_at', '<', now()->subDays($days_old))
            ->get();
        $this->info("Found {$update_requests->count()} update requests to delete.");

        $update_requests->each(fn($update_request) => $update_request->delete());
        $this->info("Deleted {$update_requests->count()} update requests.");
        return 0;
    }
}
