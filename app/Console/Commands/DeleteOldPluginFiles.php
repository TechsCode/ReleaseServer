<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOldPluginFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:old-plugin-files {--days-old=7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old plugin files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days_old = $this->option('days-old') ?? 7;

        $this->info("Deleting plugin files older than {$days_old} days...");
        $files = Storage::disk('plugins')->allFiles();
        $deleted_file_count = 0;

        foreach ($files as $file) {
            if (Storage::disk('plugins')->lastModified($file) < now()->subDays($days_old)->getTimestamp()) {
                Storage::disk('plugins')->delete($file);
                $deleted_file_count++;
            }
        }

        $this->info("Deleted $deleted_file_count plugin files older than {$days_old} days.");
        return 0;
    }
}
