@props([ 'key' => 'success'])

@if (session($key))
<div 
    x-data="{ show: true }" 
    x-init="setTimeout(() => show = false, 3000)" 
    x-show="show" 
    x-transition.opacity.duration.1000ms
    {{ $attributes->merge(['class'
        => 'fixed bottom-5 right-5 z-50 rounded-lg bg-green-600 px-5 py-3
        text-white shadow-lg']) }}
    >
    {{ session($key) }}
</div>
@endif