@props([
    'tag' => 'div',
])

<{{ $tag }} {{ $attributes->merge(['class' => 'border border-border rounded-lg bg-card p-4 md:text-sm']) }} >
    {{ $slot }}
</{{ $tag }}>