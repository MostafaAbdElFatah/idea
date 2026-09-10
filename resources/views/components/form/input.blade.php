@props([
'name',
'label',
'type' => 'text',
'value' => null,
'autocomplete' => null,
'placeholder' => null,
])


<div {{ $attributes->only('class')->merge(['class' => 'flex flex-col items-start space-y-2']) }}>
    <label for="{{ $name }}" class="label">
        {{ $label }}
    </label>

    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
        autocomplete="{{ $autocomplete }}" {{ $attributes->except('class') }}
    class="input @error($name) border-error focus:border-error focus:ring-error/15 @enderror"
    placeholder="{{ $placeholder ?? $label }}"
    {{-- required --}}
    >

    <x-form.error :name="$name" />
</div>