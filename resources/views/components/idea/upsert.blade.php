@props([
'method' => 'POST',
'action',
])

<x-dialog name="create-idea" title="New idea">
    <x-form :action="$action" :method="$method" class="space-y-6">
        <x-form.input autofacus label="Title" name="title" type="email" placeholder="Enter a title for your idea" />

        <x-form.input autofacus label="Description" name="description" type="textarea"
            placeholder="Describe your idea" />

        <x-form.input autofacus label="Title" name="title" type="file" placeholder="Enter a title for your idea" />

    </x-form>
</x-dialog>