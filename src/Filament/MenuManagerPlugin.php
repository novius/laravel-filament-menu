<?php

namespace Novius\LaravelFilamentMenu\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use InvalidArgumentException;
use Novius\LaravelFilamentMenu\Facades\MenuManager;

class MenuManagerPlugin implements Plugin
{
    /**
     * Fails fast when the configured resources are not Filament resources.
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        MenuManager::getMenuResource();
        MenuManager::getMenuItemResource();
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'laravel-filament-menu';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MenuManager::getMenuResource(),
            MenuManager::getMenuItemResource(),
        ]);
    }

    public function boot(Panel $panel): void {}
}
