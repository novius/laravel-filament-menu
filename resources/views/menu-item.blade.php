@php
    use Novius\LaravelFilamentMenu\Enums\LinkType;
    use Novius\LaravelFilamentMenu\Models\Menu;
    use Novius\LaravelFilamentMenu\Models\MenuItem;

    /** @var Menu $menu */
    /** @var MenuItem $item */
@endphp
<li @class($itemContainerClasses) @if ($item->children->isNotEmpty()) data-has-children="true" @endif>
    @if ($item->link_type === LinkType::html)
        {!! $item->html !!}
    @elseif ($item->link_type !== LinkType::empty)
        <a href="{{ $item->href() }}"
            @class([
                ...$itemClasses,
                $menu->template->isActiveItem($item) ? $itemActiveClasses : '',
                $item->html_classes
            ])
            @if ($item->target_blank) target="_blank" rel="noopener noreferrer" @endif
            @if ($menu->template->isActiveItem($item)) data-active="true" @endif
        >
            {{ $item->title }}
        </a>
    @else
        <x-laravel-filament-menu::dynamic-tag :tag="$itemEmptyTag"
            @class([
                ...$itemClasses,
                $item->html_classes
            ])
        >
            {{ $item->title }}
        </x-laravel-filament-menu::dynamic-tag>
    @endif

    @if ($item->children->isNotEmpty())
        <ul
            data-depth="{{ $item->depth + 1 }}"
            @if ($menu->template->containsActiveItem($item)) data-active-items="true" @endif
            @class([
                ...$listClasses,
                $menu->template->containsActiveItem($item) ? $itemContainsActiveClasses : '',
            ])
        >
            @foreach($item->children as $item)
                {!! $menu->template->renderItem(
                    $menu,
                    $item,
                    $listClasses,
                    $itemContainerClasses,
                    $itemClasses,
                    $itemEmptyTag,
                    $itemActiveClasses,
                    $itemContainsActiveClasses,
                ) !!}
            @endforeach
        </ul>
    @endif
</li>
