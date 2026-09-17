@props([
'idea',
'label' => null,
])

@if ($idea->imageUrl)
<div {{ $attributes->merge(['class' => 'overflow-hidden']) }}>
    @if ($label)
    <label class="label mb-4 font-bold text-xl">{{ $label }}</label>
    @endif
    <img src="{{ $idea->imageUrl }}" alt="{{ $idea->title }}" class="w-full h-auto object-cover">
</div>
@endif