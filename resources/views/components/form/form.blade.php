@props([
'showError' => false,
'method' => 'GET',
'action',
])

<form action="{{ $action }}" method="{{ strtolower($method) === 'get' ? 'GET' : 'POST' }}" {{ $attributes }}>
    @csrf

    @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
    @method(strtoupper($method))
    @endif


    @if ($showError && $errors->any())
    <div class="rounded-md border border-red-00 bg-red-50 p-4 text-red-700">
        <ul class="list-disc pl-5 text-start text-sm text-red-600">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{ $slot }}
</form>