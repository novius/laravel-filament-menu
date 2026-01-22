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
        ?string $locale,
        protected Closure|array|string|null $containerClasses = ['lfm-'.$menuSlug],
        protected Closure|array|string|null $titleClasses = ['lfm-title'],
        protected Closure|string|null $titleTag = 'span',
        protected Closure|array|string|null $listClasses = ['lfm-items-container'],
        protected Closure|array|string|null $listRootClasses = ['lfm--is-root'],
        protected Closure|array|string|null $itemContainerClasses = ['lfm-item-li'],
        protected Closure|array|string|null $itemClasses = ['lfm-item'],
        protected Closure|string|null $itemEmptyTag = 'span',
        protected ?string $itemActiveClasses = null,
        protected ?string $itemContainsActiveClasses = null,
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
            $this->containerClasses,
            $this->titleClasses,
            $this->titleTag,
            $this->listClasses,
            $this->itemContainerClasses,
            $this->itemClasses,
            $this->itemEmptyTag,
            $this->itemActiveClasses,
            $this->itemContainsActiveClasses,
        );
    }
}
