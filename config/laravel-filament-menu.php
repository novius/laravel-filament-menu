<?php

use Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\MenuItemResource;
use Novius\LaravelFilamentMenu\Filament\Resources\Menus\MenuResource;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Novius\LaravelFilamentMenu\Templates\MenuTemplateWithoutTitle;
use Novius\LaravelFilamentMenu\Templates\MenuTemplateWithTitle;

return [
    // The menu manager will load automaticaly templates from this directory
    'autoload_templates_in' => app_path('Menus/Templates'),

    // List of tempates, other than those automatically loaded by `autoload_templates_in`
    'templates' => [
        MenuTemplateWithTitle::class,
        MenuTemplateWithoutTitle::class,
    ],

    /*
     * Resources used to manage your menus.
     */
    'resources' => [
        'menu' => MenuResource::class,
        'menu_item' => MenuItemResource::class,
    ],

    /*
     * Models used to manage your posts.
     */
    'models' => [
        'menu' => Menu::class,
        'menu_item' => MenuItem::class,
    ],
];
