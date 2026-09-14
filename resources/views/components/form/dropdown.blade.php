@props([
'name',
'options' => [],
'selected' => null,
'placeholder' => 'Select an option',
'submitOnChange' => false,
'total' => null,
])

@php
$normalized = collect($options)
->map(fn ($option) => [
'value' => (string) ($option['value'] ?? ''),
'label' => (string) ($option['label'] ?? ''),
'count' => $option['count'] ?? null,
])
->values();

$selectedValue = (string) ($selected ?? '');

$selectedOption = $normalized->firstWhere('value', $selectedValue);

$selectedLabel = $selectedOption['label'] ?? $placeholder;

$hasCounts = $normalized->contains(fn ($option) => ! is_null($option['count']));

$countsMap = $normalized->mapWithKeys(fn ($option) => [$option['value'] => $option['count']]);

$totalCount = $total ?? $normalized->sum('count');
@endphp

<div x-data="{
        open: false,
        value: @js($selectedValue),
        label: @js($selectedLabel),
        counts: @js($countsMap),
        total: @js($totalCount),

        get selectedCount() {
            return this.value === '' ? this.total : this.counts[this.value];
        },

        select(value, label) {
            this.value = value;
            this.label = label || @js($placeholder);
            this.open = false;

            @if ($submitOnChange)
                this.$nextTick(() => {
                    this.$refs.field.form?.requestSubmit?.();
                });
            @endif
        }
    }" @keydown.escape.window="open = false" class="relative w-full">
    {{-- Hidden form field --}}
    <input type="hidden" name="{{ $name }}" x-ref="field" :value="value">

    {{-- Trigger --}}
    <button type="button" @click="open = !open" :aria-expanded="open" class="group flex w-full items-center justify-between
               gap-3 rounded-lg
               border border-border
               bg-card
               px-3.5 py-2.5
               text-sm font-medium
               text-foreground
               outline-none
               transition-colors
               hover:border-white/15
               hover:bg-white/2
               focus:border-primary/40">
        <span x-text="label" class="truncate" :class="value === ''
                ? 'text-muted-foreground'
                : 'text-foreground'"></span>

        <span class="flex shrink-0 items-center gap-2.5">
            @if ($hasCounts)
            <span x-show="selectedCount != null" x-text="selectedCount" class="rounded-full bg-white/8 px-2 py-0.5 text-xs
                           font-semibold tabular-nums text-muted-foreground"></span>
            @endif

            <svg class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                :class="{ 'rotate-180 text-primary': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 1.04l-4.25-4.5a.75.75 0 01.02-1.06z"
                    clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1" @click.outside="open = false" class="absolute left-0 right-0 z-50 mt-2
               overflow-hidden rounded-lg
               border border-border
               bg-card
               shadow-2xl">
        <div class="dropdown-scrollbar max-h-64 overflow-y-auto p-1">

            {{-- Placeholder / All --}}
            <button type="button" @click="select('', '')" class="flex w-full items-center justify-between
                       gap-4 rounded-md px-3 py-2.5
                       text-sm transition-colors
                       hover:bg-white/4" :class="value === ''
                    ? 'text-primary'
                    : 'text-muted-foreground'">
                <span>
                    {{ $placeholder }}
                </span>

                <span class="flex shrink-0 items-center gap-3">
                    @if ($hasCounts)
                    <span class="text-xs tabular-nums text-muted-foreground">
                        {{ $totalCount }}
                    </span>
                    @endif

                    <svg class="h-4 w-4 shrink-0 text-primary opacity-0 transition-opacity duration-150"
                        :class="value === '' ? 'opacity-100' : 'opacity-0'" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true                     viewBox=" 0 0 20 20" ">

                        <path fill-rule=" evenodd"
                        d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.408 0l-4-4a1 1 0 011.408-1.42L8.5 12.086l6.796-6.796a1 1 0 011.408 0z"
                        clip-rule="evenodd" />
                    </svg>


                </span>
            </button>

            {{-- Options --}}
            @foreach ($normalized as $option)
            @php
            $optionValue = $option['value'];
            $optionLabel = $option['label'];
            @endphp

            <button type="button" @click="select(
                        @js($optionValue),
                        @js($optionLabel)
                    )" class="group flex w-full items-center
                           justify-between gap-4
                           rounded-md px-3 py-2.5
                           text-sm transition-colors
                           hover:bg-white/4" :class="value === '{{ $optionValue }}'
                        ? 'text-primary'
                        : 'text-foreground'">
                <span class="truncate">
                    {{ $optionLabel }}
                </span>

                <span class="flex shrink-0 items-center gap-3">


                    @if (! is_null($option['count']))
                    <span class="text-xs tabular-nums
                                       text-muted-foreground">
                        {{ $option['count'] }}
                    </span>
                    @endif

                    <svg class="h-4 w-4 shrink-0 text-primary opacity-0 transition-opacity duration-150"
                        :class="value === '{{ $optionValue }}' ? 'opacity-100' : 'opacity-0'" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M16.704 5.29a1 1 0 011.42 0l-7.5 7.5a1 1 0 01-1.408 0l-4-4a1 1 0 011.408-1.42L8.5 12.086l6.796-6.796a1 1 0 011.408 0z"
                            clip-rule="evenodd" />
                    </svg>


                </span>
            </button>
            @endforeach

        </div>
    </div>
</div>