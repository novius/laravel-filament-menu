<?php

use Illuminate\Support\Facades\Route;
use Novius\LaravelFilamentMenu\Enums\LinkType;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Novius\LaravelFilamentMenu\Tests\Support\TestMenu as Menu;

beforeEach(function () {
    $this->menu = Menu::query()->create([
        'name' => 'main',
        'locale' => 'en',
        'template' => 'with-title',
        'title' => 'Main navigation',
    ]);
});

it('génère le href d\'un lien interne pointant vers une route', function () {
    Route::get('/contact', fn () => 'contact')->name('contact');
    Route::getRoutes()->refreshNameLookups();

    $item = MenuItem::query()->create([
        'title' => 'Contact',
        'menu_id' => $this->menu->id,
        'link_type' => LinkType::internal_link,
        'internal_route' => 'route:contact',
    ]);

    expect($item->fresh()->href())->toBe(url('/contact'));
});

it('retombe sur # quand la route d\'un lien interne n\'existe pas', function () {
    $item = MenuItem::query()->create([
        'title' => 'Unknown',
        'menu_id' => $this->menu->id,
        'link_type' => LinkType::internal_link,
        'internal_route' => 'route:unknown',
    ]);

    expect($item->fresh()->href())->toBe('#');
});
