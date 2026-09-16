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
    <x-form
        :action="$action"
        :method="$method"
        class="dropdown-scrollbar max-h-[90dvh] overflow-y-auto p-1 space-y-6"
        x-data="ideaForm({
            links: {{ json_encode(collect(old('links', []))->map(fn ($value) => is_scalar($value) ? (string) $value : '')->filter()->values()->all()) }},
            steps: {{ json_encode(collect(old('steps', []))->map(fn ($value) => is_scalar($value) ? (string) $value : '')->filter()->values()->all()) }}
        })"
    >
        <x-form.input autofacus label="Title" name="title" placeholder="Enter a title for your idea" />
        
        {{-- Idea Status --}}
        <div class="w-full sm:w-[calc(50%-12px)]">
            <label for="status" class="mb-2 block text-xs font-semibold uppercase tracking-widest text-gray-400">
                Status
            </label>

            <x-form.dropdown name="status" :options="$statusOptions" placeholder="All statuses" />
        </div>

        <x-form.input autofacus label="Description" name="description" type="textarea"
            placeholder="Describe your idea" :required=false />



         <!-- Steps -->
        <x-form.input autofacus label="Image" name="image" type="file" placeholder="Enter a title for your idea" :required="false"/>

                <div class="space-y-3">
            <label for="step" class="label">Actionable Steps</label>

            <div class="flex gap-x-3">
                <input
                    id="step"
                    type="text"
                    x-model="step"
                    x-ref="step"
                    @input="stepError = ''"
                    autocomplete="off"
                    spellcheck="false"
                    @keydown.enter.prevent="addStep()"
                    placeholder="What needs to be done?"
                    class="input min-w-0 flex-1"
                >

                <button
                    type="button"
                    class="btn btn-outlined"
                    @click="addStep()"
                    :disabled="!step.trim()"
                    aria-label="Add step"
                >
                    <x-icons.close class="rotate-45 form-muted-icon" />
                </button>
            </div>

            <p x-cloak x-show="stepError" x-text="stepError" class="text-sm text-error"></p>

            <template x-for="(stepText, index) in steps" :key="stepText">
                <div class="flex items-center justify-between gap-x-3 py-2">
                    <span class="input min-w-0 flex-1 truncate text-sm text-primary" x-text="stepText"></span>

                    <input type="hidden" name="steps[]" :value="stepText">

                    <button
                        type="button"
                        class="btn btn-outlined"
                        @click="removeStep(index)"
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
                <input
                    id="url"
                    type="url"
                    x-model="url"
                    x-ref="url"
                    @input="urlError = ''"
                    autocomplete="url"
                    spellcheck="false"
                    @keydown.enter.prevent="addLink()"
                    placeholder="https://example.com"
                    class="input min-w-0 flex-1"
                >

                <button
                    type="button"
                    class="btn btn-outlined"
                    @click="addLink()"
                    :disabled="!url.trim()"
                    aria-label="Add link"
                >
                    <x-icons.close class="rotate-45 form-muted-icon" />
                </button>
            </div>

            <p x-cloak x-show="urlError" x-text="urlError" class="text-sm text-error"></p>

            <template x-for="(link, index) in links" :key="link">
                <div class="flex items-center justify-between gap-x-3 py-2">
                    <a 
                        :href="link" 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        class="input min-w-0 flex-1 truncate text-sm text-primary hover:underline"
                        x-text="link"></a>

                    <input type="hidden" name="links[]" :value="link">

                    <button 
                        type="button" 
                        class="btn btn-outlined"
                        @click="removeLink(index)" 
                        :aria-label="`Remove ${link}`">
                        <x-icons.close class="form-muted-icon" />
                    </button>
                </div>
            </template>

            <x-form.error name="links" />
        </div>

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