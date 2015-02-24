<?php

namespace KikFinder\BanHammer;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{

    public function register()
    {
        $this->app->bindIf('banhammer.hammer', function ($app) {
            return new Hammer($app);
        });
    }

    public function boot()
    {
        // Publish our configuration file.
        $this->publishes([
            __DIR__ . '/resources/configs/banhammer.php' => config_path('banhammer.php'),
        ], 'config');

        // Publish our migrations
        $this->publishes([
            __DIR__ . '/resources/database/migrations/' => base_path('/database/migrations')
        ], 'migrations');
    }
}
