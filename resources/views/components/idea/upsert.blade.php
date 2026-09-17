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
        <div class="space-y-3">
            <label for="image" class="label">Feature Image</label>

            <input
                id="image"
                name="image"
                type="file"
                accept="image/*"
                x-ref="image"
                @change="previewImage($event)"
                class="sr-only"
            >

            {{-- Preview of the newly selected image --}}
            <div x-show="imagePreview" x-cloak class="group relative overflow-hidden rounded-lg">
                <img :src="imagePreview" alt="Selected image preview" class="w-full h-auto max-h-80 object-cover">

                <div class="pointer-events-none absolute inset-x-0 top-0 h-24 bg-linear-to-b from-black/60 to-transparent"></div>

                <span class="absolute left-3 top-3 rounded-full bg-primary/90 px-2.5 py-1 text-xs font-semibold text-primary-foreground backdrop-blur-md">
                    New image
                </span>

                <div class="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-black/60 p-1 text-xs text-white ring-1 ring-white/15 backdrop-blur-md">
                    <label for="image" class="cursor-pointer rounded-full px-2.5 py-1 font-medium transition hover:bg-white/10">
                        Change
                    </label>
                    <button
                        type="button"
                        @click="clearImage()"
                        class="rounded-full p-1.5 transition hover:bg-error"
                        aria-label="Clear selected image"
                    >
                        <x-icons.close width="12" height="12" />
                    </button>
                </div>
            </div>

            @if ($idea->imageUrl)
            {{-- Current image --}}
            <div x-show="! imagePreview" class="space-y-2">
                <x-idea.image :idea="$idea" remove-form="remove-idea-image" class="rounded-lg" />

                <label for="image" class="inline-flex cursor-pointer text-sm font-medium text-primary hover:underline">
                    Replace image
                </label>
            </div>
            @else
            {{-- Placeholder --}}
            <label
                for="image"
                x-show="! imagePreview"
                class="group flex h-44 cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-border bg-card text-muted-foreground transition hover:border-primary/50 hover:bg-primary/5 hover:text-foreground @error('image') border-error @enderror"
            >
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10 transition group-hover:bg-primary/15 group-hover:text-primary">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="m21 15-5-5L5 21" />
                    </svg>
                </span>
                <span class="text-sm font-medium">Click to add an image</span>
                <span class="text-xs">PNG, JPG or WEBP up to 5MB</span>
            </label>
            @endif

            <x-form.error name="image" />
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
                {{ $idea->exists ? 'Update' : 'Create'}}
            </button>

        </div>
    </x-form>

    @if ($idea->imageUrl)
    <x-form id="remove-idea-image" :action="route('idea.image.destroy', $idea)" method="DELETE" class="hidden">
        <input type="hidden" name="reopen_dialog" value="1">
    </x-form>
    @endif
</x-dialog>