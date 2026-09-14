@props([ 'statusCounts' ])
@php
    $statusOptions = collect(\App\Enums\IdeaStatus::cases())
        ->map(fn ($status) => [
            'value' => $status->value,
            'label' => $status->label(),
            'count' => $statusCounts[$status->value],
        ])
        ->all();
@endphp

<div class="mt-6">
    <form method="GET" action="{{ route('ideas') }}">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">

            {{-- Status Filter --}}
            <div class="w-full sm:w-[calc(50%-12px)]">
                <label
                    for="status"
                    class="mb-2 block text-xs font-semibold uppercase tracking-widest text-gray-400"
                >
                    Filter by status
                </label>

                <x-form.dropdown
                    name="status"
                    :options="$statusOptions"
                    :selected="strtolower(trim(request('status', '')))"
                    placeholder="All statuses"
                    :submit-on-change="true"
                />
            </div>

            {{-- Active Filter --}}
            @if (request('status'))
                <a
                    href="{{ route('ideas') }}"
                    class="group mx-2 inline-flex h-11 items-center gap-2 rounded-xl
                           border border-white/10 bg-white/4 px-4
                           text-sm font-medium text-gray-400
                           transition-all duration-200
                           hover:border-red-400/20 hover:bg-red-400/10
                           hover:text-red-300"
                >
                    <svg
                        class="h-4 w-4 transition-transform duration-200 group-hover:rotate-90"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            d="M5.23 5.23a.75.75 0 011.06 0L10 8.94l3.71-3.71a.75.75 0 111.06 1.06L11.06 10l3.71 3.71a.75.75 0 11-1.06 1.06L8.94 10 5.23 6.29a.75.75 0 010-1.06z"
                        />
                    </svg>

                    Clear filter
                </a>
            @endif

        </div>
    </form>
</div>