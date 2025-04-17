<?php

namespace App\Http\Controllers;

use App\Models\post_views;
use App\Http\Requests\Storepost_viewsRequest;
use App\Http\Requests\Updatepost_viewsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostViewsController extends Controller
{
    public function recordView(Request $request, $postId)
    {
        $userId = auth()->id();
        $sessionKey = $userId ? "user:$userId" : "guest:" . $request->ip();
        $viewedKey = "post:viewed:$postId:$sessionKey";
        $queueKey = "post:views:queue:$postId:$sessionKey";

        if (!Cache::has($viewedKey)) {
            $views = Cache::get("post:views:$postId", 0) + 1;
            Cache::put("post:views:$postId", $views, now()->addHours(24));

            $viewData = [
                'post_id' => $postId,
                'user_id' => $userId,
                'viewed_at' => Carbon::now()->toDateTimeString(),
            ];
            Cache::put($queueKey, json_encode($viewData), now()->addHours(24));
            $queueKeys = Cache::get('post:views:queue:keys', []);
            if (!in_array($queueKey, $queueKeys)) {
                $queueKeys[] = $queueKey;
                Cache::put('post:views:queue:keys', $queueKeys, now()->addHours(24));
            }
            Cache::put($viewedKey, 1, now()->addMinutes(5));
        }

        return response()->json(['message' => 'View recorded']);
    }

    public function getPostView($postId)
    {
        $views = post_views::where('post_id', $postId)->count();
        return response()->json(['post_id' => $postId, 'views' => $views], 200);
    }

    public function getTotalViews()
    {
        $totalViews = post_views::count();
        return response()->json(['views' => $totalViews], 200);
    }

}
