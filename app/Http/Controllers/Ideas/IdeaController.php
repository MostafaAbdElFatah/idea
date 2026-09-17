<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ideas;

use App\Actions\CreateIdea;
use App\Enums\IdeaStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIdeaRequest;
use App\Models\Idea;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Idea::class);

        $validStatyses = IdeaStatus::values();
        $validated = request()->validate([
            'status' => ['nullable', Rule::in($validStatyses)],
        ]);

        $user = Auth::user();
        $ideas = $user
            ->ideas()
            ->latest()
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', strtolower(trim($status))))
            ->paginate(20);

        return view('idea.index', [
            'ideas' => $ideas,
            'statusCounts' => $user->statusCounts(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdeaRequest $request, CreateIdea $createIdea): RedirectResponse
    {
        // $validated = $request->safe()->except(['steps', 'image']); //->except('steps');

        // $steps = $request['steps'] ?? [];
        // unset($request['steps']);

        // $imagePath = $request->file('image')?->store('ideas', 'public');

        // $data = collect($validated)
        //     ->except(['steps', 'image'])
        //     ->reject(fn (mixed $value): bool => $value === null)
        //     ->put('status', $attributes['status'] ?? IdeaStatus::PENDING->value)
        //     ->put('image_path', $imagePath)
        //     ->all();

        // $idea = $request->user()->ideas()->create($data);

        // $idea->steps()->createMany(
        //     collect($steps)
        //         ->map(fn ($description) => trim((string) $description))
        //         ->filter()
        //         ->map(fn ($description) => [
        //             'description' => $description,
        //         ])
        //         ->all()
        // );
        $idea = $createIdea->handle($request->user(), $request->validated(), $request->file('image'));

        return redirect()
            ->route('idea.show', $idea)
            ->with('success', 'Idea created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea): View
    {
        Gate::authorize('view', $idea);

        return view('idea.show', [
            'idea' => $idea,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea): RedirectResponse
    {
        Gate::authorize('delete', $idea);

        $idea->delete();

        return redirect()
            ->route('idea.index')
            ->with('success', 'Idea deleted successfully.');
    }
}
