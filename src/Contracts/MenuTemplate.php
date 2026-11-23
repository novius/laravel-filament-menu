<?php

namespace Novius\LaravelFilamentMenu\Contracts;

use Closure;
use Filament\Schemas\Components\Component;
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

    /** @return array<Component> */
    public function fields(): array;

    /** @return array<string, string> */
    public function casts(): array;

    /** @return view-string */
    public function view(): string;

    /** @return view-string */
    public function viewItem(): string;

    /**
     * @param  Closure|array<array-key, mixed>|string|null  $containerClasses
     * @param  Closure|array<array-key, mixed>|string|null  $titleClasses
     * @param  Closure|array<array-key, mixed>|string|null  $listClasses
     * @param  Closure|array<array-key, mixed>|string|null  $listRootClasses
     * @param  Closure|array<array-key, mixed>|string|null  $itemContainerClasses
     * @param  Closure|array<array-key, mixed>|string|null  $itemClasses
     */
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

    /**
     * @param  Closure|array<array-key, mixed>|string|null  $listClasses
     * @param  Closure|array<array-key, mixed>|string|null  $itemContainerClasses
     * @param  Closure|array<array-key, mixed>|string|null  $itemClasses
     */
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
