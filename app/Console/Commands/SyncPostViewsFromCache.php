<?php

namespace App\Console\Commands;

use App\Models\post_views;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncPostViewsFromCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-post-views-from-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queueKeys = Cache::get('post:views:queue:keys', []);
        if (empty($queueKeys)) {
            $this->info('No post views to sync.');
            return;
        }

        foreach ($queueKeys as $key) {
            if (Cache::has($key)) {
                $viewData = json_decode(Cache::get($key), true);
                post_views::create([
                    'post_id' => $viewData['post_id'],
                    'user_id' => $viewData['user_id'],
                    'viewed_at' => $viewData['viewed_at'],
                ]);
                Cache::forget($key);
            }
        }

        Cache::forget('post:views:queue:keys');

        $this->info('Post views synced from cache successfully.');
    }
}
