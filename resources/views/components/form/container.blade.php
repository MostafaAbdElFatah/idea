@props([
'title' => null,
'description' => null,
'method' => null,
'action',
])

<div {{ $attributes->merge([
    'class' => 'flex min-h-[calc(90vh-4rem)] items-center justify-center px-4',
    ]) }}>
    <div class="w-full max-w-md">
        <div class="text-center">
            @if ($title)
            <h1 class="text-3xl font-bold tracking-tight">
                {{ $title }}
            </h1>
            @endif

            @if ($description)
            <p class="mt-1 text-muted-foreground">
                {{ $description }}
            </p>
            @endif

            <x-form :action="$action" :method="$method" :show-error="true" class="mt-10 space-y-4">
                {{ $slot }}
            </x-form>
        </div>
    </div>
</div>