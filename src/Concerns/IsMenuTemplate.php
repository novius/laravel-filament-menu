<?php

namespace Novius\LaravelFilamentMenu\Concerns;

use Closure;
use Kalnoy\Nestedset\Collection;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Throwable;

trait IsMenuTemplate
{
    public function hasTitle(): bool
    {
        return false;
    }

    public function maxDepth(): int
    {
        return 1;
    }

    public function isActiveItem(MenuItem $item): bool
    {
        return $item->href() === url()->current();
    }

    public function containsActiveItem(MenuItem $item): bool
    {
        foreach ($item->descendants as $descendant) {
            if ($this->isActiveItem($descendant)) {
                return true;
            }
        }

        return false;
    }

    public function fields(): array
    {
        return [];
    }

    public function casts(): array
    {
        return [];
    }

    abstract public function view(): string;

    /**
     * @throws Throwable
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
    ): string {
        $containerClasses = (array) (is_callable($containerClasses) ? $containerClasses($menu) : $containerClasses);
        $titleClasses = (array) (is_callable($titleClasses) ? $titleClasses($menu) : $titleClasses);
        $listClassesCallback = static fn (?MenuItem $item = null) => (array) (is_callable($listClasses) ? $listClasses($item) : $listClasses);
        $listRootClassesCallback = static fn (?MenuItem $item = null) => (array) (is_callable($listRootClasses) ? $listRootClasses($item) : $listRootClasses);

        return view($this->view(), [
            'menu' => $menu,
            'items' => $items,
            'containerClasses' => $containerClasses,
            'titleClasses' => $titleClasses,
            'titleTag' => $titleTag,
            'listClasses' => $listClassesCallback,
            'listRootClasses' => $listRootClassesCallback,
            'itemContainerClasses' => $itemContainerClasses,
            'itemClasses' => $itemClasses,
            'itemEmptyTag' => $itemEmptyTag,
            'itemActiveClasses' => $itemActiveClasses,
            'itemContainsActiveClasses' => $itemContainsActiveClasses,
        ])->render();
    }

    abstract public function viewItem(): string;

    /**
     * @throws Throwable
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
    ): string {
        $listClasses = (array) (is_callable($listClasses) ? $listClasses($item) : $listClasses);
        $itemContainerClasses = (array) (is_callable($itemContainerClasses) ? $itemContainerClasses($item) : $itemContainerClasses);
        $itemClasses = (array) (is_callable($itemClasses) ? $itemClasses($item) : $itemClasses);

        return view($this->viewItem(), [
            'menu' => $menu,
            'item' => $item,
            'listClasses' => $listClasses,
            'itemContainerClasses' => $itemContainerClasses,
            'itemClasses' => $itemClasses,
            'itemEmptyTag' => $itemEmptyTag,
            'itemActiveClasses' => $itemActiveClasses,
            'itemContainsActiveClasses' => $itemContainsActiveClasses,
        ])->render();
    }
}
