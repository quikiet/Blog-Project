<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostLikesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\PostTagController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsiteSettingController;
use App\Http\Middleware\AuthenticationMiddleware;
use App\Models\refuseReasons;
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

Route::middleware([AuthenticationMiddleware::class])->group(function () {
    Route::apiResource('posts', PostsController::class)->only(['index', 'show'])->parameters([
        'posts' => 'slug'
    ]);
});

Route::delete('authors/bulk', [AuthorsController::class, 'bulkDelete']);

Route::apiResource('authors', AuthorsController::class)->parameters([
    'authors' => 'slug'
])->except('bulkDelete');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('categories', CategoriesController::class)->parameters([
        'categories' => 'slug'
    ]);
    Route::apiResource('posts', PostsController::class)->except(['index', 'show'])->parameters([
        'posts' => 'slug'
    ]);
    // Route::apiResource('authors', AuthorsController::class)->parameters([
    //     'authors' => 'slug'
    // ])->except(['methods: index', 'show']);
});


Route::post('/upload-image', [PostsController::class, 'uploadImage']);


Route::apiResource('refuse-reasons', refuseReasons::class);
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
