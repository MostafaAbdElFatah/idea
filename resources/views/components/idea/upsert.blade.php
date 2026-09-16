@props([
    'method' => 'POST',
    'action',
])

@php
    $statusOptions = collect(\App\Enums\IdeaStatus::cases())
        ->map(fn ($status) => [
        'value' => $status->value,
        'label' => $status->label(),
        ])
        ->all();
@endphp


<x-dialog name="create-idea" title="New idea">
    <x-form :action="$action" :method="$method" class="dropdown-scrollbar max-h-[90dvh] overflow-y-auto p-1 space-y-6">
        <x-form.input autofacus label="Title" name="title" placeholder="Enter a title for your idea" />

        <x-form.input autofacus label="Description" name="description" type="textarea"
            placeholder="Describe your idea" />

        {{-- Idea Status --}}
        <div class="w-full sm:w-[calc(50%-12px)]">
            <label for="status" class="mb-2 block text-xs font-semibold uppercase tracking-widest text-gray-400">
                Status
            </label>

            <x-form.dropdown name="status" :options="$statusOptions" placeholder="All statuses" />
        </div>

        <x-form.input autofacus label="Image" name="image" type="file" placeholder="Enter a title for your idea" :required="false"/>

        <div class="mt-6 flex justify-end gap-3">

            <button type="button" class="btn btn-outlined">
                Cancel
            </button>

            <button type="submit" class="btn">
                Create
            </button>
            
        </div>
    </x-form>
</x-dialog>