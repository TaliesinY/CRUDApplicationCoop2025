<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AiService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AiService::class, function ($app) {
            return new AiService();
        });
    }

    public function boot()
    {
        //
    }
}
