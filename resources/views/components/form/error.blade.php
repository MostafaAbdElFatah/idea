@props(['name'])

@error($name)
<p class="text-sm text-error">
    {{ $message }}
</p>
@enderror