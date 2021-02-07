<?php

namespace Pestopancake\LaravelBackpackNotifications\Tests;

use Backpack\CRUD\BackpackServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Pestopancake\LaravelBackpackNotifications\LaravelBackpackNotificationsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            BackpackServiceProvider::class,
            LaravelBackpackNotificationsServiceProvider::class,
        ];
    }
}
