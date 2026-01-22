@php
    use Novius\LaravelFilamentMenu\Models\Menu;

    /** @var Menu $menu */
@endphp

<nav role="navigation"
    id="menu-{{ $menu->slug }}"
    aria-label="{{ $menu->aria_label ?? $menu->title ?? $menu->name }}"
    @class(['lfm-'.$menu->slug])
>
    @if ($menu->template->hasTitle())
        <{{$titleTag}} class="lfm-title">{{ $menu->title ?? $menu->name }}</{{$titleTag}}>
    @endif

    <ul class="lfm-items-container lfm--is-root" data-depth="0">
        @foreach($items as $item)
            {!! $menu->template->renderItem($menu, $item, $itemEmptyTag) !!}
        @endforeach
    </ul>
</nav>
