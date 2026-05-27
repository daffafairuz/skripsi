<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\SiteDevicesUpdated;
use App\Listeners\SyncSiteDevicesToMqtt;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            \URL::forceScheme('https');
        }

        Event::listen(
            SiteDevicesUpdated::class,
            SyncSiteDevicesToMqtt::class
        );
    }
}
