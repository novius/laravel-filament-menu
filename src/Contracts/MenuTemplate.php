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
        Closure|array|string|null $containerClasses = null,
        Closure|array|string|null $titleClasses = null,
        Closure|string|null $titleTag = 'span',
        Closure|array|string|null $listClasses = null,
        Closure|array|string|null $listRootClasses = null,
        Closure|array|string|null $itemContainerClasses = null,
        Closure|array|string|null $itemClasses = null,
        Closure|string|null $itemEmptyTag = 'span',
        ?string $itemActiveClasses = null,
        ?string $itemContainsActiveClasses = null,
    ): string;

    public function renderItem(
        Menu $menu,
        MenuItem $item,
        Closure|array|string|null $listClasses = null,
        Closure|array|string|null $itemContainerClasses = null,
        Closure|array|string|null $itemClasses = null,
        Closure|string|null $itemEmptyTag = 'span',
        ?string $itemActiveClasses = null,
        ?string $itemContainsActiveClasses = null,
    ): string;
}
