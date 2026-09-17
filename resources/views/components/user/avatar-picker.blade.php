@props([
    'name' => 'profile_image',
    'currentUrl' => null,
    'initials' => null,
    'removeForm' => null,
])

<div
    x-data="avatarPicker(@js($currentUrl))"
    {{ $attributes->merge(['class' => 'flex flex-col items-center gap-3']) }}
>
    <label for="{{ $name }}" class="group relative block h-28 w-28 cursor-pointer rounded-full">
        <span class="relative flex h-full w-full items-center justify-center overflow-hidden rounded-full border border-border bg-card ring-4 ring-background transition group-hover:border-primary/60">
            <img x-show="preview" x-cloak :src="preview" alt="Profile photo preview" class="h-full w-full object-cover">

            <span x-show="! preview" class="flex h-full w-full items-center justify-center bg-white/5 text-3xl font-semibold text-foreground">
                @if ($initials)
                {{ $initials }}
                @else
                <svg class="h-10 w-10 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="8" r="4" />
                    <path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6" />
                </svg>
                @endif
            </span>

            {{-- Hover overlay --}}
            <span class="absolute inset-0 flex flex-col items-center justify-center gap-1 bg-black/55 text-xs font-medium text-white opacity-0 backdrop-blur-[2px] transition duration-200 group-hover:opacity-100">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 8h3l2-3h6l2 3h3v11H4z" />
                    <circle cx="12" cy="13" r="3.5" />
                </svg>
                <span x-text="preview ? 'Change photo' : 'Add photo'"></span>
            </span>
        </span>

        {{-- Camera badge --}}
        <span class="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full border-2 border-background bg-primary text-primary-foreground shadow-lg transition group-hover:scale-110">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                <path d="M12 5v14M5 12h14" />
            </svg>
        </span>
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="file"
        accept="image/*"
        x-ref="input"
        @change="pick($event)"
        class="sr-only"
    >

    <div class="flex flex-col items-center gap-1 text-center text-xs">
        <button type="button" x-show="selected" x-cloak @click="clear()" class="font-medium text-muted-foreground transition hover:text-foreground">
            Undo
        </button>

        @if ($removeForm)
        <button type="button" x-show="! selected" @click="confirmRemove = true" class="font-medium text-error/90 transition hover:text-error">
            Remove photo
        </button>
        @endif

        <span x-show="! selected" class="text-muted-foreground">JPG, PNG or WEBP · max 2MB</span>
    </div>

    <x-form.error :name="$name" />

    @if ($removeForm)
    <x-dialog.confirm-dialog
        title="Remove profile photo?"
        message="Your initials will be shown instead until you add a new photo."
        confirmLabel="Remove photo"
        confirmClass="bg-error text-white hover:brightness-110"
        :form="$removeForm"
        state="confirmRemove"
    />
    @endif
</div>
