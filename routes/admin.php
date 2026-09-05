<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImagesController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\PostTrashController;

Route::middleware(['can:author'])->group(function () {
    Route::resource('posts', PostController::class)->except('show');
    Route::resource('images', ImagesController::class)->only(['store', 'destroy']);
});

Route::middleware(['can:moderator'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
});

Route::middleware(['can:dev'])->group(function () {
    Route::resource('tags', TagController::class)->only('create', 'store', 'edit', 'update', 'destroy');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('post-trash', PostTrashController::class)->except('show', 'create', 'store', 'edit');
    Route::resource('products', ProductController::class)->except(['show']);
});
