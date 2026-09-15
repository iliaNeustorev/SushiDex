<?php

namespace App\Providers;

use App\Helpers\SystemHelper;
use App\Integrations\Sms\SmsAeroAdapter;
use App\Interfaces\SmsSendInterface;
use App\Interfaces\SystemHelperInterface;
use Illuminate\Support\ServiceProvider;
use Tests\Mocks\MockSmsSendAdapter;
use Tests\Mocks\MockSystemHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $smsAdapter = $this->app->environment('local', 'testing')
            ? MockSmsSendAdapter::class
            : SmsAeroAdapter::class;

        $this->app->bind(SystemHelperInterface::class, SystemHelper::class);
        $this->app->bind(SmsSendInterface::class, $smsAdapter);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $this->app->bind(SystemHelperInterface::class, MockSystemHelper::class);
    }
}
