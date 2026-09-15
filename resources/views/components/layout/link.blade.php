@props([
    'route',
    'title',
    'width' => null,
    'height' => null,
])

<a href="{{ $route }}" {{ $attributes->merge(['class' => 'flex items-center gap-x-2 text-sm font-medium']) }}>
    <x-icons.icon-text icon="arrow-back" :title="$title" :width="$width" :height="$height" />
</a>