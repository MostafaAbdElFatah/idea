@props(['name', 'bag' => 'default'])

@error($name, $bag)
<p class="text-sm text-error">
    {{ $message }}
</p>
@enderror
