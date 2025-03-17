<?php

namespace App\Providers;

use App\Models\posts;
use App\Policies\PostsPolicy;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    protected $policies = [
        posts::class => PostsPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
