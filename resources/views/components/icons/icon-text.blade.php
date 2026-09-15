@props([
'icon',
'title',
'width' => null,
'height' => null,
])

<x-dynamic-component :component="'icons.' . $icon" :width="$width" :height="$height" />
{{ $title }}