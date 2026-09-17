<x-layout.card tag="a" href="{{ route('idea.show', $idea) }}" class="block">
    <x-idea.image :idea="$idea" class="mb-2 -mx-4 -mt-4 rounded-t-lg" />
    <h3 class="text-foreground text-lg">{{ $idea->id }} - {{ $idea->title }}</h3>
    <x-idea.status-label :status="$idea->status" class="mt-4" />
    <div class="mt-5 line-clamp-3"> {{ $idea->description }} </div>
    <div class="mt-4"> {{ $idea->created_at->diffForHumans() }} </div>
</x-layout.card>