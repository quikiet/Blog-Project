<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PostLikesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\PostTagController;
use App\Http\Controllers\TagsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisteredUserController::class, 'store']);
// ->middleware('guest')



Route::apiResource('categories', CategoriesController::class);
Route::apiResource('posts', PostsController::class);
Route::apiResource('comments', CommentsController::class);
Route::apiResource('tags', TagsController::class);


Route::apiResource('post_tag', PostTagController::class);
Route::get('posts/{postId}/tags', [PostTagController::class, 'getTagsByPost']);


Route::apiResource("post_likes", PostLikesController::class);
