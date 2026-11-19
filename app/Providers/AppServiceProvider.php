<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {


// Source - https://stackoverflow.com/a/77918619
// Posted by Rinaldi Pratama
// Retrieved 2026-11-19, License - CC BY-SA 4.0

        if (env(key: 'APP_ENV') === 'local' && request()->server(key: 'HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme(scheme: 'https');
        }


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.gamebase');
    }
}
