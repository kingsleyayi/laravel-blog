<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

Route::prefix('api')->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
});