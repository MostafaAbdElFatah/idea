@props(['status'])

@php
$classes = "inline-block rounded-full border  px-2 py-1 text-xs font-medium";
$color = $status->color();
$classes .= " bg-{$color}-500/10 text-{$color}-500 border-{$color}-500/20";;
@endphp


<div>
    <span {{ $attributes(['class'=> $classes]) }}>
        {{ $status->label() }}
    </span>
</div>