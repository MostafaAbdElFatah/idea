<x-layout>
    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2">Capture your throughts, Make a plan.</p>
        </header>

        <x-ideas.status-filter :statusCounts="$statusCounts" />

        <div class="mt-10 text-muted-foreground">
            @if ($ideas->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 mb-10">
                @foreach ($ideas as $idea)
                <x-ideas.card :idea="$idea" />
                @endforeach
            </div>
            @else
            <x-ideas.empty-list :filtered="request()->filled('status')" :status="request('status')" />
            @endif
        </div>
    </div>
</x-layout>