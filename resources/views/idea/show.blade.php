<x-layout>
    <div class="py-8 max-w-4xl mx-auto" x-data="{ deleteDialogOpen: false }">
        <div class="flex justify-between items-center">
            <x-layout.link :route="route('idea.index', $idea)" icon="arrow-back" title="Back to Ideas" />
            <div class="flex items-center space-x-4">

                <x-layout.button 
                    @click="$dispatch('open-model', 'edit-idea')"
                    icon="arrow-back" 
                    title="Edit Idea" 
                    icon='external'
                    width=16 
                    height=16 
                />

                <x-layout.button type="button" @click="deleteDialogOpen = true" title="Delete" icon="trash" :width="16"
                    :height="16" class="text-red-500" />
            </div>
        </div>

        <x-idea.image :idea="$idea" class="rounded-lg mt-10" />

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

        @if ($idea->steps->count())
        <div>
            <h3 class="mt-6 text-xl font-bold">Actionable Steps</h3>

            <div class="mt-3 space-y-3">
                @foreach ($idea->steps as $step)
                <x-layout.card class="font-medium">
                    <x-form :action="route('steps.update', $step)" method="PATCH">
                        <div class="flex items-center gap-x-3">
                            <button type="submit" role="checkbox"
                                class="flex size-5 items-center justify-center rounded-sm text-primary-foreground {{ $step->completed ? 'bg-primary' : 'border border-primary' }}">
                                &check;
                            </button>

                            <span class="{{ $step->completed ? 'line-through text-muted-foreground' : '' }}">{{
                                $step->description }}</span>
                        </div>
                    </x-form>
                </x-layout.card>
                @endforeach
            </div>
        </div>
        @endif

        @if ($idea->links->count())
        <div>
            <h3 class="font-bold text-xl mt-6">Links</h3>
            <div class="mt-3 space-y-3">
                @foreach ($idea->links as $link)

                <x-layout.card>

                    <x-layout.link target="_blank" :route="$link" :title="$link" icon="external" width=18 height=18
                        class="text-primary font-medium flex gap-x-3 items-center truncate text-sm hover:underline" />
                </x-layout.card>
                @endforeach
            </div>
        </div>
        @endif


        {{-- edit dailog --}}
        <x-idea.upsert action="{{ route('idea.edit', $idea) }}" :idea="$idea" />

            
        {{-- confirm delete dailog --}}

        <x-dialog.confirm-dialog title="Delete Idea?"
            message="Are you sure you want to delete this idea? This action cannot be undone."
            confirmClass="btn bg-red-500 text-white hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700"
            confirmLabel="Delete" method="DELETE" state="deleteDialogOpen" :action="route('idea.delete', $idea)" />

    </div>
</x-layout>