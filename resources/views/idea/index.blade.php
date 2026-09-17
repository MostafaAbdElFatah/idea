<x-layout>
    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2">Capture your throughts, Make a plan.</p>
        </header>

        <x-layout.card 
            tag="button" 
            type="button" 
            x-data 
            @click="$dispatch('open-model', 'create-idea')"
            class="cursor-pointer h-32 w-full text-start"
        >
            <p>What's the idea?</p>
        </x-layout.card>

        <x-idea.status-filter :statusCounts="$statusCounts" />

        <div class="mt-10 text-muted-foreground">
            @if ($ideas->isNotEmpty())
            <div x-data="masonryGrid" class="grid auto-rows-[1px] items-start gap-x-6 md:grid-cols-2 mb-10">
                @foreach ($ideas as $idea)
                <x-idea.card :idea="$idea" />
                @endforeach
            </div>
            @else
            <x-idea.empty-list :filtered="request()->filled('status')" :status="request('status')" />
            @endif
        </div>

        <x-idea.upsert action="{{ route('idea.store') }}" />

    </div>
</x-layout>