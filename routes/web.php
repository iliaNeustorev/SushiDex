<?php

use App\Http\Controllers\GeneralController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)->only('index', 'show');
Route::get('/menu', [GeneralController::class, 'menu'])->name('menu');
Route::get('/profile', [GeneralController::class, 'profile'])->name('profile');
Route::get('/', [GeneralController::class, 'index']);
