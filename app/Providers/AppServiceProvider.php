<?php

namespace App\Providers;

use App\Services\AiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the AiService class as a singleton
        $this->app->singleton(AiService::class, function () {
            return new AiService();
        });
    }

    public function boot()
    {
        //
    }
}
