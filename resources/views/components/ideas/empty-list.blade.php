@props([
'filtered' => false,
'status' => null,
])

<div class="flex min-h-[calc(70vh-4rem)] items-center justify-center px-4">
    <x-layout.card class="p-16" role="status">
        <div class="flex flex-col items-center gap-3">
            @if ($filtered)
            <svg class="h-10 w-10 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 6.75h18M6.75 6.75v11.25A2.25 2.25 0 009 20.25h6a2.25 2.25 0 002.25-2.25V6.75M9 10.5v6m6-6v6" />
            </svg>

            <p class="text-sm font-medium">
                No ideas found
            </p>

            <p class="text-xs text-base-content/60">
                There are no ideas with the
                <span class="font-medium text-base-content">{{ $status }}</span>
                status.
            </p>

            <a href="/" class="btn btn-primary btn-sm mt-2">
                Clear Filter
            </a>
            @else
            <svg class="h-10 w-10 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 0H6.375A3.375 3.375 0 003 5.625v12.75A3.375 3.375 0 006.375 21.75h11.25A3.375 3.375 0 0021 18.375V16.5m-13.5-9V4.5m0 3h3m-3 3h6m-6 3h6" />
            </svg>

            <p class="text-sm font-medium">
                No ideas yet
            </p>

            <p class="text-xs text-base-content/60">
                Create your first idea to get started.
            </p>

            <a href="/ideas/create" class="btn btn-primary btn-sm mt-2">
                + Create Your First Idea
            </a>
            @endif
        </div>
    </x-layout.card>
</div>