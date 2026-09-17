@props([
    'name',
    'label',
    'description' => null,
    'value' => '1',
    'checked' => false,
])

<label for="{{ $name }}" {{ $attributes->only('class')->merge(['class' => 'group flex w-fit cursor-pointer select-none items-start gap-3']) }}>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="checkbox"
        value="{{ $value }}"
        @checked(old($name, $checked))
        {{ $attributes->except('class') }}
        class="peer sr-only"
    >

    <span
        aria-hidden="true"
        class="relative flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-border bg-card
               shadow-[inset_0_1px_2px_rgb(0_0_0/0.4)]
               transition-all duration-200 ease-out
               group-hover:border-primary/50 group-active:scale-90
               peer-checked:border-primary peer-checked:bg-primary peer-checked:shadow-[0_0_0_4px_color-mix(in_oklch,var(--color-primary)_18%,transparent),0_0_14px_-2px_var(--color-primary)]
               peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-offset-background
               peer-checked:[&>svg]:scale-100 peer-checked:[&>svg]:opacity-100 peer-checked:[&>svg_path]:[stroke-dashoffset:0]"
    >
        <svg
            class="h-3.5 w-3.5 scale-50 text-primary-foreground opacity-0 transition-all duration-200 ease-out"
            viewBox="0 0 16 16"
            fill="none"
        >
            <path
                d="M3.5 8.5l3 3 6-7"
                stroke="currentColor"
                stroke-width="2.25"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="transition-[stroke-dashoffset] delay-75 duration-300 ease-out [stroke-dasharray:16] [stroke-dashoffset:16]"
            />
        </svg>
    </span>

    <span class="flex flex-col items-start text-left leading-tight">
        <span class="text-sm font-medium text-muted-foreground transition-colors group-hover:text-foreground group-has-checked:text-foreground">
            {{ $label }}
        </span>

        @if ($description)
        <span class="text-xs text-muted-foreground/70">{{ $description }}</span>
        @endif
    </span>
</label>
