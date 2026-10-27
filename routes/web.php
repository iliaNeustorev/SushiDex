<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\PhoneController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\RemittanceController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\EnsurePaymentWebhookIp;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;

// EVENT
Route::post('/remittances/callbacks', [RemittanceController::class, 'callback'])->withoutMiddleware(HandleInertiaRequests::class)->middleware(EnsurePaymentWebhookIp::class);
// MAIN
Route::resource('posts', PostController::class)->only('index', 'show');
Route::get('/menu', [GeneralController::class, 'menu'])->name('menu');
Route::get('/', [GeneralController::class, 'index'])->name('home');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::middleware(['auth', 'can:is-blocked'])->group(function () {
    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::resource('notifications', NotificationController::class)->only(['index', 'update']);
    Route::prefix('posts')->group(function () {
        Route::put('/{post}/moderating', [PostController::class, 'moderating'])->name('posts.moderating');
    });
    Route::prefix('profile')->group(function () {
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
        Route::put('/changePassword', [ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::delete('/delete-profile', [ProfileController::class, 'destroyProfile'])->name('profile.delete-profile');
    });

    Route::prefix('cart')->group(function () {
        Route::put('/update', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/delete', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::put('/sync-temp-cart', [CartController::class, 'syncWithTemp'])->name('cart.sync-temp-cart');
    });
    Route::resource('orders', OrderController::class)->only(['index', 'store', 'show'])->whereNumber('order');
    Route::post('orders/{order}/payment', [RemittanceController::class, 'store'])->name('orders.payment.store')->whereNumber('order');
    Route::patch('orders/{order}/update-settings', [AdminOrderController::class, 'updateSettings'])->name('orders.settings.update')->whereNumber('order');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel')->whereNumber('order');
    Route::patch('users/{user}/change-address', [UserController::class, 'changeAddress'])->name('users.change-address')->whereNumber('user');
});
