<?php

namespace App\Providers;

use App\Helpers\SystemHelper;
use App\Interfaces\SystemHelperInterface;
use Illuminate\Support\ServiceProvider;
use Tests\Mocks\MockSystemHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SystemHelperInterface::class, SystemHelper::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $this->app->bind(SystemHelperInterface::class, MockSystemHelper::class);
    }
}
