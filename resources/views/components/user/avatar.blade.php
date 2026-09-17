@props([
    'user',
    'size' => 'h-9 w-9 text-xs',
])

<span {{ $attributes->merge(['class' => "relative inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-border font-semibold uppercase tracking-wide text-foreground {$size}"]) }}>
    @if ($user->profileImageUrl)
    <img src="{{ $user->profileImageUrl }}" alt="{{ $user->fullName }}" class="h-full w-full object-cover">
    @else
    <span aria-label="{{ $user->fullName }}">{{ $user->initials }}</span>
    @endif
</span>
