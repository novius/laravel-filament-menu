<?php

use Novius\LaravelFilamentMenu\Filament\MenuManagerPlugin;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\MenuItemResource;
use Novius\LaravelFilamentMenu\Filament\Resources\Menus\MenuResource;
use Novius\LaravelFilamentMenu\Services\MenuManagerService;

function menuManagerWithResources(array $resources): MenuManagerService
{
    return new MenuManagerService(['resources' => $resources]);
}

it('retourne les ressources par défaut', function () {
    $service = menuManagerWithResources([]);

    expect($service->getMenuResource())->toBe(MenuResource::class)
        ->and($service->getMenuItemResource())->toBe(MenuItemResource::class);
});

it('utilise la ressource de menu définie dans la config', function () {
    $customResource = get_class(new class extends MenuResource {});

    expect(menuManagerWithResources(['menu' => $customResource])->getMenuResource())->toBe($customResource);
});

it('refuse une ressource qui n\'est pas une ressource Filament', function () {
    menuManagerWithResources(['menu_item' => stdClass::class])->getMenuItemResource();
})->throws(InvalidArgumentException::class, 'The menu item resource must be a subclass of');

it('refuse de construire le plugin avec une ressource invalide', function () {
    config()->set('laravel-filament-menu.resources.menu', stdClass::class);
    app()->forgetInstance(MenuManagerService::class);

    new MenuManagerPlugin;
})->throws(InvalidArgumentException::class, 'The menu resource must be a subclass of');
