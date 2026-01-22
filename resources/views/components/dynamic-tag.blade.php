@props(['tag' => 'div'])

<{{ $tag }} {{ $attributes }}>
    {{ $slot }}
</{{ $tag }}>
