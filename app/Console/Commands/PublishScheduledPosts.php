<?php

namespace App\Console\Commands;

use App\Models\posts;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật trạng thái bài viết từ scheduled -> published nếu đến thời gian';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        try {
            $updated = posts::where('status', 'scheduled')
                ->where('published_at', '<=', Carbon::now())
                ->update(['status' => 'published', 'published_at' => Carbon::now()]);

            $this->info("$updated bài viết đã được cập nhật.");
        } catch (\Exception $e) {
            \Log::error("Error updating post status: {$e->getMessage()}");
            $this->error("An error occurred: {$e->getMessage()}");
        }
    }
}
