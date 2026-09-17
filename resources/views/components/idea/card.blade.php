<x-layout.card tag="a" href="{{ route('idea.show', $idea) }}" class="block">
    @if ($idea->imageUrl)
    <!-- $idea->imageUrl or Storage::url($idea->image_path) or asset('storage/' . $idea->image_path) -->
    <div class="mb-2 -mx-4 -mt-4 rounded-t-lg overflow-hidden">
        <img src="{{ $idea->imageUrl }}" alt="{{ $idea->title }}" class="w-full h-auto object-cover">
    </div>
    @endif
    <h3 class="text-foreground text-lg">{{ $idea->title }}</h3>
    <x-idea.status-label :status="$idea->status" class="mt-4" />
    <div class="mt-5 line-clamp-3"> {{ $idea->description }} </div>
    <div class="mt-4"> {{ $idea->created_at->diffForHumans() }} </div>
</x-layout.card>