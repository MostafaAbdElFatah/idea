@props([
'name' => 'password',
'label' => 'Password',
'value' => null,
'autocomplete' => 'current-password',
'placeholder' => 'At least 8 characters',
'showStrength' => false,
])

<div {{ $attributes->only('class')->merge(['class' => 'flex flex-col items-start space-y-2']) }}>
    <label for="{{ $name }}" class="label">
        {{ $label }}
    </label>

    <div class="relative w-full">
        <input id="{{ $name }}" name="{{ $name }}" type="password" value="{{ old($name, $value) }}"
            autocomplete="{{ $autocomplete }}" {{ $attributes->except('class') }}
        class="input pr-12 @error($name) border-red-600 focus:border-red-600 focus:ring-red-600/15 @enderror"
        placeholder="{{ $placeholder }}"
        required
        >

        <button type="button" data-password-toggle="{{ $name }}"
            class="absolute right-1 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center text-muted-foreground transition hover:text-primary focus:outline-none"
            aria-label="Show {{ strtolower($label) }}" aria-pressed="false">
            <svg data-password-eye="hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z" />
                <circle cx="12" cy="12" r="2.75" />
            </svg>

            <svg data-password-eye="shown" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58a2 2 0 0 0 2.84 2.84" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.88 4.24A10.94 10.94 0 0 1 12 4c6.25 0 9.75 8 9.75 8a16.8 16.8 0 0 1-3.08 4.1M6.61 6.61C3.75 8.25 2.25 12 2.25 12s3.5 6 9.75 6c1.35 0 2.57-.3 3.67-.77" />
            </svg>
        </button>
    </div>

    @if ($showStrength)
    <div data-password-strength="{{ $name }}" class="hidden space-y-2 pt-1" aria-live="polite">
        <div class="flex w-full gap-1.5" aria-hidden="true">
            @foreach (range(1, 4) as $segment)
            <span data-strength-segment="{{ $segment }}"
                class="block h-1.5 min-w-0 flex-1 rounded-full bg-input"></span>
            @endforeach
        </div>
        <div class="flex items-center justify-between gap-3 text-xs">
            <span data-strength-label class="font-semibold text-muted-foreground">Very weak</span>
            <span data-strength-hint class="text-right text-muted-foreground">Use 12+ characters with a mix of letters,
                numbers, and symbols.</span>
        </div>
    </div>
    @endif
    

    <x-form.error :name="$name" />


</div>