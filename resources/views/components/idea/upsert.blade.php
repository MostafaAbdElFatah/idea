@props([
'method' => 'POST',
'action',
'idea'=> new App\Models\Idea(),
])

@php
$statusOptions = collect(\App\Enums\IdeaStatus::cases())
->map(fn ($status) => [
'value' => $status->value,
'label' => $status->label(),
])
->all();

$oldList = fn (string $key, iterable $default = []): array => collect(old($key, $default))
->filter(fn ($value) => is_scalar($value) && $value !== '')
->map(fn ($value) => (string) $value)
->values()
->all();

$initialFormState = [
'links' => $oldList('links', $idea->links?->getArrayCopy() ?? []),
'steps' => $oldList('steps', $idea->steps->pluck('description')),
];
@endphp


<x-dialog name="{{ $idea->exists ? 'edit-idea' : 'create-idea'}}" title="{{ $idea->exists ? 'Edit idea' : 'New idea'}}">
    <x-form :action="$action" :method="$method" class="p-1 space-y-6"
        x-bind:enctype="hasImage ? 'multipart/form-data' : 'application/x-www-form-urlencoded'"
        x-data="ideaForm({{ \Illuminate\Support\Js::from($initialFormState) }})">
        <x-form.input autofacus label="Title" name="title" value="{{ $idea->title }}"
            placeholder="Enter a title for your idea" />

        {{-- Idea Status --}}
        <div class="w-full sm:w-[calc(50%-12px)]">
            <label for="status" class="mb-2 block text-xs font-semibold uppercase tracking-widest text-gray-400">
                Status
            </label>

            <x-form.dropdown name="status" :options="$statusOptions" :selected="old('status', $idea->status->value)"
                placeholder="All statuses" />
        </div>

        <x-form.input autofacus label="Description" name="description" type="textarea" value="{{ $idea->description }}"
            placeholder="Describe your idea" :required=false />


        <!-- image -->
        <div class="space-y-2">
            
            <x-idea.image :idea="$idea" label="Feature Image" class="rounded-lg mt-10" />

            <button 
                type="button" 
                class="btn btn-outlined mt-2 mb-6 h-14 w-full "
                @click="" 
                :aria-label="`Remove Image`"
            >
             Remove Image
            </button>

            <x-form.input autofacus label="Image" name="image" type="file" accept="image/*" :required="false"
                x-on:change="hasImage = $event.target.files.length > 0" />
        </div>


        <!-- Steps -->

        <div class="space-y-3">
            <label for="step" class="label">Actionable Steps</label>

            <div class="flex gap-x-3">
                <input id="step" type="text" x-model="step" x-ref="step" @input="stepError = ''" autocomplete="off"
                    spellcheck="false" @keydown.enter.prevent="addStep()" placeholder="What needs to be done?"
                    class="input min-w-0 flex-1">

                <button type="button" class="btn btn-outlined" @click="addStep()" :disabled="!step.trim()"
                    aria-label="Add step">
                    <x-icons.close class="rotate-45 form-muted-icon" />
                </button>
            </div>

            <p x-cloak x-show="stepError" x-text="stepError" class="text-sm text-error"></p>

            <template x-for="(stepText, index) in steps" :key="stepText">
                <div class="flex items-center justify-between gap-x-3 py-2">
                    <span class="input min-w-0 flex-1 truncate text-sm text-primary" x-text="stepText"></span>

                    <input type="hidden" name="steps[]" :value="stepText">

                    <button type="button" class="btn btn-outlined" @click="removeStep(index)"
                        :aria-label="`Remove ${stepText}`">
                        <x-icons.close class="form-muted-icon" />
                    </button>
                </div>
            </template>

            <x-form.error name="steps" />
        </div>
        <!-- Linkes -->
        <div class="space-y-3">
            <label for="url" class="label">Links</label>

            <div class="flex gap-x-3">
                <input id="url" type="url" x-model="url" x-ref="url" @input="urlError = ''" autocomplete="url"
                    spellcheck="false" @keydown.enter.prevent="addLink()" placeholder="https://example.com"
                    class="input min-w-0 flex-1">

                <button type="button" class="btn btn-outlined" @click="addLink()" :disabled="!url.trim()"
                    aria-label="Add link">
                    <x-icons.close class="rotate-45 form-muted-icon" />
                </button>
            </div>

            <p x-cloak x-show="urlError" x-text="urlError" class="text-sm text-error"></p>

            <template x-for="(link, index) in links" :key="link">
                <div class="flex items-center justify-between gap-x-3 py-2">
                    <a :href="link" target="_blank" rel="noopener noreferrer"
                        class="input min-w-0 flex-1 truncate text-sm text-primary hover:underline" x-text="link"></a>

                    <input type="hidden" name="links[]" :value="link">

                    <button type="button" class="btn btn-outlined" @click="removeLink(index)"
                        :aria-label="`Remove ${link}`">
                        <x-icons.close class="form-muted-icon" />
                    </button>
                </div>
            </template>

            <x-form.error name="links" />
        </div>

        <div class="mt-6 flex justify-end gap-3">

            <button type="button" class="btn btn-outlined" @click="show = false">
                Cancel
            </button>

            <button type="submit" class="btn">
                {{ $idea->exists ? 'Edit' : 'Create'}}
            </button>

        </div>
    </x-form>
</x-dialog>