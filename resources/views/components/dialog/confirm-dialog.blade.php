@props([
'title' => null,
'message' => null,
'method' => null,
'confirmClass' = '',
'confirmLabel',
'action',
'state'
])

{{-- 'title' => 'Delete Item?',
'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
'state' => 'confirmDialogOpen', --}}

<div x-show="{{ $state }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div @click.outside="{{ $state }} = false"
        class="w-full max-w-md rounded-lg bg-card border border-border p-6 shadow-xl">


        @if ($title)
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $title }}
        </h2>
        @endif


        @if ($message)
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            {{ $message }}
        </p>
        @endif


        <div class="mt-6 flex justify-end gap-3">

            <button type="button" @click="{{ $state }} = false" class="btn btn-outlined">
                Cancel
            </button>


            <x-layout.button :action="$action" :method="$method" title="Edit Idea" icon='trash' :width=16 height=16 />


            <form action="{{ $action }}" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit" class="$confromClass">
                    {{ $confirmLabel }}
                </button>
            </form>

        </div>
    </div>
</div>