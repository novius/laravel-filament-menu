@php
    use Novius\LaravelFilamentMenu\Enums\LinkType;
    use Novius\LaravelFilamentMenu\Models\Menu;
    use Novius\LaravelFilamentMenu\Models\MenuItem;

    /** @var Menu $menu */
    /** @var MenuItem $item */
@endphp
<li class="lfm-item-li" @if ($item->children->isNotEmpty()) data-has-children="true" @endif>
    @if ($item->link_type === LinkType::html)
        {!! $item->html !!}
    @elseif ($item->link_type !== LinkType::empty)
        <a href="{{ $item->href() }}"
            @class([ 'lfm-item', $item->html_classes])
            @if ($item->target_blank) target="_blank" rel="noopener noreferrer" @endif
            @if ($menu->template->isActiveItem($item)) data-active="true" @endif
        >
            {{ $item->title }}
        </a>
    @else
        <{{$itemEmptyTag}} @class(['lfm-item', $item->html_classes])>
            {{ $item->title }}
        </{{$itemEmptyTag}}>
    @endif

    @if ($item->children->isNotEmpty())
        <ul class="lfm-items-container"
            data-depth="{{ $item->depth + 1 }}"
            @if ($menu->template->containsActiveItem($item)) data-active-items="true" @endif
        >
            @foreach($item->children as $item)
                {!! $menu->template->renderItem($menu, $item, $itemEmptyTag) !!}
            @endforeach
        </ul>
    @endif
</li>
