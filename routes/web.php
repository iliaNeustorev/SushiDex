<?php

use App\Http\Controllers\Client\PhoneController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)->only('index', 'show');
Route::get('/menu', [GeneralController::class, 'menu'])->name('menu');
Route::get('/', [GeneralController::class, 'index']);
Route::prefix('profile')->middleware('auth')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/', [ProfileController::class, 'update'])->name('profile.update');
    Route::prefix('phone')->group(function () {
        Route::post('/', [PhoneController::class, 'store'])->name('profile.phone-store');
        Route::post('/send-code', [PhoneController::class, 'sendCode'])->name('profile.phone-sendCode');
        Route::post('/confirm-code', [PhoneController::class, 'confirmCode'])->name('profile.phone-confirmCode');
        Route::delete('/{pendingPhone}', [PhoneController::class, 'destroy'])->name('profile.phone-destroy');
    });
    Route::post('/change-avatar', [ProfileController::class, 'changeAvatar'])->name('profile.change-avatar');
    Route::delete('/delete-avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.delete-avatar');
});
