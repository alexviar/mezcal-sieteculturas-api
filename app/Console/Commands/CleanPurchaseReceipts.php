<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanExpiredReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchases:clean-receipts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired temporary report files older than 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = Storage::disk('local');
        $reportsPath = 'purchases/';

        if (!$disk->exists($reportsPath)) {
            $this->info('Reports directory does not exist.');
            return 0;
        }

        $files = $disk->files($reportsPath);
        $deletedCount = 0;
        $expiredTime = now()->subMinutes(30);

        foreach ($files as $file) {
            $fileTime = $disk->lastModified($file);
            $fileDate = \Carbon\Carbon::createFromTimestamp($fileTime);

            if ($fileDate->lte($expiredTime)) {
                $disk->delete($file);
                $deletedCount++;
                $this->line("Deleted expired file: {$file}");
            }
        }

        if ($deletedCount > 0) {
            $this->info("Successfully deleted {$deletedCount} expired report files.");
        } else {
            $this->info('No expired report files found.');
        }

        return 0;
    }
}
