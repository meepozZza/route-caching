<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('orders', \App\Http\Controllers\OrderController::class);
Route::apiResource('posts', \App\Http\Controllers\PostController::class);
