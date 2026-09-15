<x-layout.card 
    tag="a" 
    href="{{ route('idea.show', $idea) }}" 
    class="block">
    <h3 class="text-foreground text-lg">{{ $idea->title }}</h3>
    <x-ideas.status-label :status="$idea->status" class="mt-4"/>
    <div class="mt-5 line-clamp-3"> {{ $idea->description }} </div>
    <div class="mt-4"> {{ $idea->created_at->diffForHumans() }} </div>
</x-layout.card>