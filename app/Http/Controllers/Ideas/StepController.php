<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ideas;

use App\Http\Controllers\Controller;
use App\Models\Step;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Step $step): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Step $step): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Step $step): RedirectResponse
    {
        Gate::authorize('update', $step);

        $step->update(['completed' => ! $step->completed]);

        return back()
            ->with('success', 'Step updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Step $step): void
    {
        //
    }
}
