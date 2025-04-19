<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__ . '/auth.php';


Route::get("/auth/google/redirect", [RegisteredUserController::class, "googleLoginRedirect"]);
Route::get('/auth/google/callback', [RegisteredUserController::class, "googleLoginCallback"]);
