<?php

namespace Pestopancake\LaravelBackpackNotifications;

use Illuminate\Support\ServiceProvider;

class LaravelBackpackNotificationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'backpack-database-notifications');

        $this->mergeConfigFrom(
            __DIR__.'/config/backpack/databasenotifications.php', 'backpack.databasenotifications'
        );

        $this->publishes([
            __DIR__.'/config/backpack/databasenotifications.php' => config_path('backpack/databasenotifications.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->bind('LaravelBackpackNotifications', function ($app) {
            return new LaravelBackpackNotificationsServiceProvider($app);
        });
    }

    public function provides()
    {
        return [];
    }
}
