<?php

namespace App\Providers;

use App\Broadcasting\FirebaseChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

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
    public function boot(): void
    {
        Notification::extend('firebase', function ($app) {
            return new FirebaseChannel();
        });
    }
}
