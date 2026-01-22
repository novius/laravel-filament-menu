@php
    use Novius\LaravelFilamentMenu\Models\Menu;

    /** @var Menu $menu */
@endphp

<nav role="navigation"
     id="menu-{{ $menu->slug }}"
     aria-label="{{ $menu->aria_label ?? $menu->title ?? $menu->name }}"
     @class($containerClasses)
>
    @if ($menu->template->hasTitle())
        <x-laravel-filament-menu::dynamic-tag :tag="$titleTag" @class($titleClasses)>
            {{ $menu->title ?? $menu->name }}
        </x-laravel-filament-menu::dynamic-tag>
    @endif
    <ul @class([...$listClasses(), ...$listRootClasses()]) data-depth="0">
        @foreach($items as $item)
            {!! $menu->template->renderItem(
                $menu,
                $item,
                $listClasses,
                $itemContainerClasses,
                $itemClasses,
                $itemEmptyTag,
                $itemActiveClasses,
                $itemContainsActiveClasses
            ) !!}
        @endforeach
    </ul>
</nav>
