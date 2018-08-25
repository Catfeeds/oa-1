<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BackstageServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'backstageapi',
            'App\Http\Components\BackstageApi\BackstageApi'
        );
    }
    /**
     * @return array
     */
    public function provides()
    {
        return ['backstageapi'];
    }
}
