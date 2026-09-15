@props([
'title',
'icon' => null,
'width' => null,
'height' => null,
'method' => null,
'action' => null,
])

@if ($action && $method)
<x-form :action="$action" :method="$method">
    <button type="submit" {{ $attributes->merge([
        'class' => 'btn btn-outlined flex items-center gap-x-2',
        ]) }}
        >
        @if ($icon)
        <x-icons.icon-text :icon="$icon" :title="$title" :width="$width" :height="$height" />
        @else
        {{ $title }}
        @endif
    </button>
</x-form>
@else
<button {{ $attributes->merge([
    'class' => 'btn btn-outlined flex items-center gap-x-2',
    ]) }}
    >
    @if ($icon)
    <x-icons.icon-text :icon="$icon" :title="$title" :width="$width" :height="$height" />
    @else
    {{ $title }}
    @endif
</button>
@endif