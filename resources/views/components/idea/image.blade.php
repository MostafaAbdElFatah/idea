@props([
    'idea',
    'label' => null,
    'removeForm' => null,
])

@if ($idea->imageUrl)
<div {{ $attributes->merge(['class' => 'overflow-hidden']) }}>
    @if ($label)
    <label class="label mb-4 font-bold text-xl">{{ $label }}</label>
    @endif

    <div
        class="group relative overflow-hidden rounded-[inherit]"
        @if ($removeForm)
        x-data="{ confirming: false }"
        @keydown.escape.stop="confirming = false"
        @endif
    >
        <img src="{{ $idea->imageUrl }}" alt="{{ $idea->title }}" class="w-full h-auto object-cover transition duration-300 group-hover:scale-[1.01]">

        @if ($removeForm)
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-24 bg-linear-to-b from-black/60 to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100 group-focus-within:opacity-100 [@media(hover:none)]:opacity-100"
            :class="confirming && 'opacity-100'"
        ></div>

        <div class="absolute right-3 top-3" @click.outside="confirming = false">
            <button
                type="button"
                x-show="! confirming"
                @click="confirming = true; $nextTick(() => $refs.confirmRemove.focus())"
                class="flex items-center gap-1.5 rounded-full bg-black/50 px-3 py-1.5 text-xs font-medium text-white ring-1 ring-white/15 backdrop-blur-md opacity-0 transition duration-200 hover:bg-error hover:ring-error focus-visible:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-error group-hover:opacity-100 [@media(hover:none)]:opacity-100"
                aria-label="Remove image"
            >
                <x-icons.trash width="12" height="12" />
                Remove
            </button>

            <div
                x-show="confirming"
                x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="flex origin-top-right items-center gap-1 rounded-full bg-black/70 py-1 pl-3 pr-1 text-xs text-white ring-1 ring-white/15 backdrop-blur-md"
                role="group"
                aria-label="Confirm image removal"
            >
                <span class="mr-1 font-medium">Remove image?</span>

                <button
                    type="button"
                    @click="confirming = false"
                    class="rounded-full px-2.5 py-1 text-white/80 transition hover:bg-white/10 hover:text-white"
                >
                    Keep
                </button>

                <button
                    type="submit"
                    form="{{ $removeForm }}"
                    x-ref="confirmRemove"
                    class="flex items-center gap-1 rounded-full bg-error px-2.5 py-1 font-medium text-white transition hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70"
                >
                    <x-icons.trash width="11" height="11" />
                    Remove
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
@endif
