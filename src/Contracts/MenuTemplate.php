<?php

namespace Novius\LaravelFilamentMenu\Contracts;

use Closure;
use Kalnoy\Nestedset\Collection;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;

interface MenuTemplate
{
    public function key(): string;

    public function name(): string;

    public function hasTitle(): bool;

    public function maxDepth(): int;

    public function isActiveItem(MenuItem $item): bool;

    public function containsActiveItem(MenuItem $item): bool;

    /** @return array<\Filament\Schemas\Components\Component> */
    public function fields(): array;

    public function casts(): array;

    public function view(): string;

    public function viewItem(): string;

    public function render(
        Menu $menu,
        Collection $items,
        Closure|string|null $titleTag = 'span',
        Closure|string|null $itemEmptyTag = 'span',
    ): string;

    public function renderItem(
        Menu $menu,
        MenuItem $item,
        Closure|string|null $itemEmptyTag = 'span',
    ): string;
}
