<?php

use App\Http\Controllers\GeneralController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)->only('index', 'show');
Route::get('/', [GeneralController::class, 'index']);
