<?php

namespace Novius\LaravelFilamentMenu\View\Components;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Kalnoy\Nestedset\Collection;
use Novius\LaravelFilamentMenu\Models\Menu as MenuModel;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class Menu extends Component
{
    protected ?MenuModel $menu = null;

    public function __construct(
        string $menuSlug,
        ?string $locale,
        protected Closure|array|string|null $containerClasses = null,
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
        $this->containerClasses = $containerClasses ?? ['lfm-container'];

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

        $itemsData = Cache::rememberForever($this->menu->getCacheName(), function () {
            return MenuItem::scoped(['menu_id' => $this->menu->id])
                ->withDepth()
                ->defaultOrder()
                ->with(['children', 'descendants', 'ancestors', 'linkable'])
                ->get()
                ->toTree()
                ->toArray();
        });
        if (! is_array($itemsData)) {
            Cache::forget($this->menu->getCacheName());

            return $this->render();
        }

        /** @var Collection<int, MenuItem> $items */
        $items = $this->hydrateMenuItems($itemsData);

        return $this->menu->template->render(
            $this->menu,
            $items,
            $this->containerClasses,
            $this->titleClasses,
            $this->titleTag,
            $this->listClasses,
            $this->listRootClasses,
            $this->itemContainerClasses,
            $this->itemClasses,
            $this->itemEmptyTag,
            $this->itemActiveClasses,
            $this->itemContainsActiveClasses,
        );
    }

    /**
     * Hydrates MenuItem models and their relations from an array.
     *
     * @param  array<int, array<string, mixed>>  $itemsData
     * @return Collection<int, MenuItem>
     */
    protected function hydrateMenuItems(array $itemsData, ?MenuItem $parent = null): Collection
    {
        $models = [];
        foreach ($itemsData as $data) {
            $childrenData = $data['children'] ?? null;
            $descendantsData = $data['descendants'] ?? null;
            $ancestorsData = $data['ancestors'] ?? null;
            $linkableData = $data['linkable'] ?? null;

            // Remove relations from attributes before hydration
            $attributes = array_diff_key($data, array_flip(['children', 'descendants', 'ancestors', 'linkable', 'menu']));
            $attributes['extras'] = $attributes['extras'] ? json_encode($attributes['extras']) : null;

            /** @var MenuItem $item */
            $item = MenuItem::hydrate([$attributes])->first();

            if ($parent) {
                $item->setRelation('parent', $parent);
            }

            // Hydrate relations explicitly
            $item->setRelation('children', is_array($childrenData) ? $this->hydrateMenuItems($childrenData, $item) : Collection::make());
            $item->setRelation('descendants', is_array($descendantsData) ? $this->hydrateMenuItems($descendantsData) : Collection::make());
            $item->setRelation('ancestors', is_array($ancestorsData) ? $this->hydrateMenuItems($ancestorsData) : Collection::make());
            $item->setRelation('menu', $this->menu);

            if (isset($item->linkable_type, $item->linkable_id) && is_array($linkableData)) {
                $linkableModel = new ($item->linkable_type);
                $item->setRelation('linkable', $linkableModel->newFromBuilder($linkableData));
            } else {
                $item->setRelation('linkable', null);
            }

            $models[] = $item;
        }

        /** @var Collection<int, MenuItem> $items */
        $items = new Collection($models);

        return $items;
    }
}
