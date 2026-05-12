<?php

namespace Novius\LaravelFilamentMenu\Tests;

use Novius\LaravelFilamentMenu\LaravelFilamentMenuServiceProvider;
use Novius\LaravelFilamentMenu\Tests\Support\TestMenu;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Sluggable\SluggableServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ...(class_exists('Spatie\Sluggable\SluggableServiceProvider') ? [SluggableServiceProvider::class] : []),
            LaravelFilamentMenuServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // In-memory SQLite for speed and isolation
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Default locale for tests
        $app['config']->set('app.locale', 'en');

        // Use the test Menu model overriding translatable locales
        $app['config']->set('laravel-filament-menu.models.menu', TestMenu::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        // Run package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
