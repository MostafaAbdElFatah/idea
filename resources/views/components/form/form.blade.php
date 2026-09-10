@props([
'title' => '',
'description' => '',
'method' => 'GET',
'action',
])

<div {{ $attributes->merge([
    'class' => 'flex min-h-[calc(90vh-4rem)] items-center justify-center px-4 space-y-6'
    ]) }}
    >
    <div class="w-full max-w-md">
        <div class="text-center">
            <h1 class="text-3xl font-bold tracking-tight">
                {{ $title }}
            </h1>
            <p class="text-muted-foreground mt-1">
                {{ $description }}
            </p>

            <form action="{{ $action }}" method="{{ strtolower($method) === 'get' ? 'GET' : 'POST' }}"
                class="mt-10 space-y-4">
                @csrf

                @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method(strtoupper($method))
                @endif

                @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 p-4">
                    <ul class="list-disc pl-5 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{ $slot }}
            </form>
        </div>
    </div>
</div>