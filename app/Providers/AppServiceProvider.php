<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    // public function boot(): void
    // {
    //     if (config('app.env') === 'production') {
    //         \Illuminate\Support\Facades\URL::forceScheme('https');
    //     }
    // }
    public function boot(): void
    {
        URL::forceScheme('https');
        
        // ✅ Trust Railway reverse proxy
        $this->app['request']->setTrustedProxies(
            ['*'],
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );
    }
}
