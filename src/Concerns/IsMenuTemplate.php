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
        Closure|string|null $titleTag = 'span',
        Closure|string|null $itemEmptyTag = 'span',
    ): string {
        $titleTag = (string) (is_callable($titleTag) ? $titleTag($menu) : $titleTag);
        $itemEmptyTag = (string) (is_callable($itemEmptyTag) ? $itemEmptyTag($menu) : $itemEmptyTag);

        return view($this->view(), [
            'menu' => $menu,
            'items' => $items,
            'titleTag' => $titleTag,
            'itemEmptyTag' => $itemEmptyTag,
        ])->render();
    }

    abstract public function viewItem(): string;

    /**
     * @throws Throwable
     */
    public function renderItem(
        Menu $menu,
        MenuItem $item,
        Closure|string|null $itemEmptyTag = 'span',
    ): string {
        $itemEmptyTag = (string) (is_callable($itemEmptyTag) ? $itemEmptyTag($item) : $itemEmptyTag);

        return view($this->viewItem(), [
            'menu' => $menu,
            'item' => $item,
            'itemEmptyTag' => $itemEmptyTag,
        ])->render();
    }
}
