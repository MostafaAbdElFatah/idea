@props([
    'name',
    'label' => false,
    'type' => 'text',
    'value' => null,
    'autocomplete' => null,
    'placeholder' => null,
    'required' => true,
])

<div {{ $attributes->only('class')->merge(['class' => 'flex flex-col items-start space-y-2']) }}>
    @if($label)
        <label for="{{ $name }}" class="label">
            {{ $label }}
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $attributes->except('class') }}
            placeholder="{{ $placeholder ?? $label }}"
            @required($required)
            class="textarea @error($name) border-error focus:border-error focus:ring-error/15 @enderror"
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            autocomplete="{{ $autocomplete }}"
            {{ $attributes->except('class') }}
            class="input @error($name) border-error focus:border-error focus:ring-error/15 @enderror"
            placeholder="{{ $placeholder ?? $label }}"
            @required($required)
        >
    @endif

    <x-form.error :name="$name" />
</div>