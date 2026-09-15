@props([
'icon' => null,
'title',
'width' => null,
'height' => null,
])

@if ($icon)
    <x-dynamic-component :component="'icons.' . $icon" :width="$width" :height="$height" />
@endif
{{ $title }}