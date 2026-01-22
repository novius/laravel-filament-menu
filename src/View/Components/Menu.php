<?php

namespace Novius\LaravelFilamentMenu\View\Components;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Novius\LaravelFilamentMenu\Models\Menu as MenuModel;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class Menu extends Component
{
    protected ?MenuModel $menu = null;

    public function __construct(
        string $menuSlug,
        ?string $locale = null,
        protected Closure|string|null $titleTag = 'span',
        protected Closure|string|null $itemEmptyTag = 'span',
    ) {
        $this->menu = MenuModel::query()
            ->where('slug', $menuSlug)
            ->where('locale', $locale ?? app()->getLocale())
            ->first();
    }

    public function render(): string
    {
        if ($this->menu === null) {
            return '';
        }

        $items = Cache::rememberForever($this->menu->getCacheName(), function () {
            /** @phpstan-ignore method.notFound */
            return MenuItem::scoped(['menu_id' => $this->menu->id])
                ->withDepth()
                ->defaultOrder()
                ->with(['children', 'descendants', 'ancestors', 'linkable'])
                ->get()
                ->toTree();
        });

        return $this->menu->template->render(
            $this->menu,
            $items,
            $this->titleTag,
            $this->itemEmptyTag,
        );
    }
}
