<?php

namespace App\Http\Controllers;

use App\Models\posts;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function stats()
    {
        // User Statistics
        $userStats = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('last_active_at', '>', now()->subDays(7))->count(),
            'userGrowthRate' => $this->calculateGrowthRate(User::class),
            'activeUsersGrowth' => $this->calculateActiveUsersGrowth(),
            'roleCounts' => [
                'admin' => User::where('role', 'admin')->count(),
                'author' => User::where('role', 'author')->count(),
                'user' => User::where('role', 'user')->count(),
            ],
            'newUsersTrend' => $this->getNewUsersTrend(7),
            'activeUsersTrend' => $this->getActiveUsersTrend(7),
            'recentUsers' => User::withCount('user_posts')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                        'created_at' => $user->created_at,
                        'posts_count' => $user->user_posts_count
                    ];
                })
        ];

        // Post Statistics
        $postStats = [
            'totalPosts' => posts::count(),
            'postGrowthRate' => $this->calculateGrowthRate(posts::class),
            'statusCounts' => [
                'draft' => posts::where('status', 'draft')->count(),
                'pending' => posts::where('status', 'pending')->count(),
                'published' => posts::where('status', 'published')->count(),
                'scheduled' => posts::where('status', 'scheduled')->count(),
                'archived' => posts::where('status', 'archived')->count(),
                'rejected' => posts::where('status', 'rejected')->count(),
            ],
            'postsTrend' => $this->getPostsTrend(30),
            'recentPosts' => posts::with(['posts_user', 'category'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'status' => $post->status,
                        'published_at' => $post->published_at ? $post->published_at : null,
                        'thumbnail' => $post->thumbnail,
                        'user' => [
                            'id' => $post->posts_user->id,
                            'name' => $post->posts_user->name
                        ],
                        'category' => $post->category ? $post->category->name : null
                    ];
                })
        ];

        return response()->json([
            'userStats' => $userStats,
            'postStats' => $postStats
        ]);
    }

    // Helper methods
    private function calculateGrowthRate($model)
    {
        $currentCount = $model::count();
        $previousCount = $model::where('created_at', '<', now()->subMonth())->count();

        return $previousCount > 0
            ? round(($currentCount - $previousCount) / $previousCount * 100, 2)
            : 100;
    }

    private function getNewUsersTrend($days)
    {
        return User::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function getActiveUsersTrend($days)
    {
        return User::where('last_active_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(last_active_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function getPostsTrend($days)
    {
        return posts::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function calculateActiveUsersGrowth()
    {
        $currentActive = User::where('last_active_at', '>', now()->subDays(7))->count();
        $previousActive = User::whereBetween('last_active_at', [now()->subDays(14), now()->subDays(7)])->count();

        return $previousActive > 0
            ? round(($currentActive - $previousActive) / $previousActive * 100, 2)
            : 100;
    }
}
