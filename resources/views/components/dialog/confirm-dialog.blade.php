@props([
'title' => null,
'message' => null,
'method' => null,
'confirmClass' => '',
'confirmLabel',
'action' => null,
'form' => null,
'state'
])

{{-- 'title' => 'Delete Item?',
'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
'state' => 'confirmDialogOpen', --}}

<div x-show="{{ $state }}" x-cloak @keydown.escape.window="{{ $state }} = false" role="alertdialog" aria-modal="true" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
    <div @click.outside="{{ $state }} = false"
        class="w-full max-w-md rounded-lg bg-card border border-border p-6 shadow-xl">


        @if ($title)
        <h2 class="text-lg font-semibold text-foreground">
            {{ $title }}
        </h2>
        @endif


        @if ($message)
        <p class="mt-2 text-sm text-muted-foreground">
            {{ $message }}
        </p>
        @endif


        <div class="mt-6 flex justify-end gap-3">

            <button type="button" @click="{{ $state }} = false" class="btn btn-outlined">
                Cancel
            </button>

            @if ($form)
            <button type="submit" form="{{ $form }}" class="btn {{ $confirmClass }}">
                {{ $confirmLabel }}
            </button>
            @else
            <x-layout.button :action="$action" :method="$method" :class="$confirmClass" :title="$confirmLabel" />
            @endif
        </div>
    </div>
</div>