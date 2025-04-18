<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PostLikesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\PostTagController;
use App\Http\Controllers\PostViewsController;
use App\Http\Controllers\RefuseReasonsController;
use App\Http\Controllers\RefusesController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsiteSettingController;
use App\Http\Middleware\AuthenticationMiddleware;
use App\Models\refuseReasons;
use App\Models\refuses;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisteredUserController::class, 'register']);
// ->middleware('guest')
Route::post('/login', [RegisteredUserController::class, 'login']);

Route::post('/logout', [RegisteredUserController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/me', [RegisteredUserController::class, 'me'])->middleware('auth:sanctum');
// ->middleware('guest')
// ->name('login');


Route::apiResource('categories', CategoriesController::class)->only(['index', 'show'])->parameters([
    'categories' => 'slug'
]);


Route::post('/posts/{postId}/view', [PostViewsController::class, 'recordView']);
Route::get('/posts/{postId}/view', [PostViewsController::class, 'getPostView']);
Route::get('/posts/total-view', [PostViewsController::class, 'getTotalViews']);


Route::get('/posts/count_post_by_category/{status}', [PostsController::class, 'getPostAmountByStatus']);
Route::get('/posts/by-month', [PostsController::class, 'getPostsByMonth']);
Route::get('/posts/featured', [PostsController::class, 'getFeaturedPost']);
Route::get('/posts/sub-features', [PostsController::class, 'getSubFeatures']);
Route::get('/posts/latest', [PostsController::class, 'getLatestPosts']);
Route::get('/posts/trending', [PostsController::class, 'getTrendingPosts']);
Route::get('/posts/pending', [PostsController::class, 'getPendingPosts']);
Route::get('/posts/archived', [PostsController::class, 'getArchivedPosts']);
Route::get('/posts/scheduled', [PostsController::class, 'getScheduledPosts']);
Route::get('/posts/search', [PostsController::class, 'search']);

Route::put('authors/restore/{slug}', [AuthorsController::class, 'restore']);
Route::get('authors/deleted', [AuthorsController::class, 'getDeletedAuthors']);
Route::delete('authors/bulk', [AuthorsController::class, 'bulkDelete']);

Route::apiResource('authors', AuthorsController::class)->parameters([
    'authors' => 'slug'
])->except('bulkDelete', 'restore', 'getDeletedAuthors');

Route::post('refuse-reasons/bulk', [RefuseReasonsController::class, 'bulkDelete']);

Route::apiResource('refuse-reasons', RefuseReasonsController::class)->except('bulkDelete');

Route::apiResource('refuses', RefusesController::class);

Route::middleware([AuthenticationMiddleware::class])->group(function () {
    Route::apiResource('posts', PostsController::class)->only(['index', 'show'])->parameters([
        'posts' => 'slug'
    ]);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('categories', CategoriesController::class)->except(['index', 'show', 'countPostByCategory'])->parameters([
        'categories' => 'slug'
    ]);
    Route::apiResource('posts', PostsController::class)->except(['index', 'show'])->parameters([
        'posts' => 'slug'
    ]);
    Route::get('notifications', [NotificationsController::class, 'getNotifications']);
    Route::get('notifications/unread-count', [NotificationsController::class, 'getUnreadCount']);
    Route::post('notifications/read/{id}', [NotificationsController::class, 'markAsRead']);
    Route::delete('notifications', [NotificationsController::class, 'deleteNotifications']);
    Route::post('notifications/readAll', [NotificationsController::class, 'readAll']);
    // Route::apiResource('authors', AuthorsController::class)->parameters([
    //     'authors' => 'slug'
    // ])->except(['methods: index', 'show']);
});


Route::post('/upload-image', [PostsController::class, 'uploadImage']);


Route::apiResource('comments', CommentsController::class);
Route::apiResource('tags', TagsController::class);


Route::post('/delete-image', function (Request $request) {
    try {
        $publicId = $request->input('publicId'); // Lấy publicId từ request

        if (!$publicId) {
            return response()->json(["error" => "Thiếu public_id"], 400);
        }
        $result = Cloudinary::destroy($publicId, ['invalidate' => true]);

        if (isset($result['result']) && $result['result'] === 'ok') {
            return response()->json(["message" => "Ảnh đã xoá thành công"], 200);
        } else {
            return response()->json(["error" => "Không thể xoá ảnh", "response" => $result], 500);
        }
    } catch (\Exception $e) {
        Log::error("Lỗi khi xóa ảnh từ Cloudinary: " . $e->getMessage());
        return response()->json(["error" => "Lỗi khi xoá ảnh", "message" => $e->getMessage()], 500);
    }
});




Route::apiResource('post_tag', PostTagController::class);
Route::get('posts/{postId}/tags', [PostTagController::class, 'getTagsByPost']);


Route::apiResource("post_likes", PostLikesController::class);


//////////////////////////////////////////
// Bach Duc Phuoc & Vuong

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'getAll']);

    Route::post('/', [UserController::class, 'store']);

    Route::delete('/confirm/{id}', [UserController::class, 'destroyConfirmPw']);

    Route::prefix('{id}')->group(function () {
        Route::get('/', [UserController::class, 'show']);

        Route::put('/', [UserController::class, 'update']);

        Route::delete('/', [UserController::class, 'destroy']);


        Route::get('/posts', [UserController::class, 'getUserPosts']);
        Route::get('/comments', [UserController::class, 'getUserComments']);
        Route::get('/likes', [UserController::class, 'getUserPostLikes']);

        Route::post('/avatar', [UserController::class, 'updateAvatar']);
        Route::delete('/avatar', [UserController::class, 'deleteAvatar']);
    });
});
Route::prefix('website-settings')->group(function () {
    Route::get('/', [WebsiteSettingController::class, 'index']);
    Route::put('/', [WebsiteSettingController::class, 'update']);
});

Route::get('/dashboard/stats', [DashboardController::class, 'stats']);