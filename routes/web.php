<?php

use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__ . '/auth.php';

Route::get('/auth/google/redirect', [GoogleController::class, 'handleGoogleRedirect']);

Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

