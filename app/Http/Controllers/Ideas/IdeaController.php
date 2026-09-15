<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ideas;

use App\Enums\IdeaStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreideaRequest;
use App\Http\Requests\UpdateideaRequest;
use App\Models\Idea;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
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

        return view('ideas.index', [
            'ideas' => $ideas,
            'statusCounts' => $user->statusCounts(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreideaRequest $request): View
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea): View
    {
        return view('ideas.show', [
            'idea' => $idea,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea): View
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateideaRequest $request, Idea $idea): View
    {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea): RedirectResponse
    {
        $idea->delete();

        return redirect()
            ->route('ideas.index')
            ->with('success', 'Idea deleted successfully.');
    }
}
