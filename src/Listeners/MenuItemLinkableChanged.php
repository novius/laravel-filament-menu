<?php

namespace Novius\LaravelFilamentMenu\Listeners;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelLinkable\Events\LinkableChanged;

class MenuItemLinkableChanged
{
    public function __construct() {}

    public function handle(LinkableChanged $event): void
    {
        $linkable = $event->linkable;

        $items = MenuManager::getMenuItemModel()::whereHasMorph(
            'linkable',
            [get_class($linkable)],
            static fn (Builder $query) => $query->where('linkable_id', $linkable->getKey())
        )->get();

        $menus_id = $items->pluck('menu_id')->unique();

        MenuManager::getMenuModel()::whereIn('id', $menus_id)
            ->each(function (Menu $menu) {
                Cache::forget($menu->getCacheName());
            });
    }
}
