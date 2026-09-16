<x-layout>
    <div class="py-8 max-w-4xl mx-auto" x-data="{ deleteDialogOpen: false }">
        <div class="flex justify-between items-center">
            <x-layout.link :route="route('idea.index', $idea)" icon="arrow-back" title="Back to Ideas" />
            <div class="flex items-center space-x-4">

                <x-layout.button :action="route('idea.edit', $idea)" icon="arrow-back" title="Edit Idea" icon='external'
                    :width=16 height=16 />

                <x-layout.button type="button" @click="deleteDialogOpen = true" title="Delete" icon="trash" :width="16"
                    :height="16" class="text-red-500" />
            </div>
        </div>
        <h1 class="font-bold text-4xl mt-6 mx-2">{{ $idea->title }}</h1>
        <div class="flex gap-x-3 items-center mt-4">
            <x-idea.status-label :status="$idea->status" />

            <div class="text-mutred-foreground text-sm"> {{ $idea->created_at->diffForHumans() }} </div>
        </div>
        <x-layout.card class="mt-6">
            <div class="text-foreground prose prose-invert max-w-none cursor-pointer">
                {{ $idea->description }}
            </div>
        </x-layout.card>

        @if ($idea->links->count())
        <div>
            <h3 class="font-bold text-xl mt-6">Links</h3>
            <div class="mt-3 space-y-3">
                @foreach ($idea->links as $link)

                <x-layout.card>
                    <x-layout.link :route="$link" :title="$link" icon="external" width=18 height=18
                        class="text-primary font-medium flex gap-x-3 items-center" />
                </x-layout.card>
                @endforeach
            </div>
        </div>
        @endif



        {{-- confirm delete dailog --}}

        <x-dialog.confirm-dialog title="Delete Idea?"
            message="Are you sure you want to delete this idea? This action cannot be undone."
            confirmClass="btn bg-red-500 text-white hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700"
            confirmLabel="Delete" method="DELETE" state="deleteDialogOpen" :action="route('idea.delete', $idea)" />

    </div>
</x-layout>