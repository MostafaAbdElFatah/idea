@props([
    'route',
    'title',
    'icon',
    'width' => null,
    'height' => null,
])

<a href="{{ $route }}" {{ $attributes->merge(['class' => 'flex items-center gap-x-2 text-sm font-medium']) }}>
    <x-icons.icon-text :icon="$icon" :title="$title" :width="$width" :height="$height" />
</a>