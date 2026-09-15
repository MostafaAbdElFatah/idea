<x-layout>
    <div class="py-8 max-w-4xl mx-auto" x-data="{ deleteDialogOpen: false }">
        <div class="flex justify-between items-center">
            <x-layout.link :route="route('ideas.index', $idea)" title="Back to Ideas" />
            <div class="flex items-center space-x-4">

                <x-layout.button :action="route('idea.edit', $idea)" title="Edit Idea" icon='external'
                    :width=16 height=16 />

                <x-layout.button type="button" @click="deleteDialogOpen = true" title="Delete" icon="trash" :width="16" :height="16"
                    class="text-red-500" />
            </div>
        </div>
        <h1 class="font-bold text-4xl mt-2">{{ $idea->title }}</h1>
        <x-layout.card class="mt-6">
            <div class="text-foreground prose prose-invert max-w-none cursor-pointer">
                {{ $idea->description }}
            </div>
        </x-layout.card>

        {{-- confirm delete dailog --}}

        <x-dialog.confirm-dialog 
            :title="Delete Idea?"
            :message=" Are you sure you want to delete this idea?
                    This action cannot be undone."
            :confirmClass="btn bg-red-500 text-white hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700"
            :confirmLabel="Delete"
            :method="DELETE"
            :state="deleteDialogOpen"
            :action="route('idea.delete', $idea)"
        />


        {{-- <div x-show="deleteDialogOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.outside="deleteDialogOpen = false" 
            class="w-full max-w-md rounded-lg bg-card border border-border p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Delete Idea?
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Are you sure you want to delete this idea?
                    This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="deleteDialogOpen = false" class="btn btn-outlined">
                        Cancel
                    </button>

                    <form  method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn bg-red-500 text-white hover:bg-red-600
                           dark:bg-red-600 dark:hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div> --}}
    </div>
</x-layout>