<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ideas;

use App\Models\Idea;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreideaRequest;
use App\Http\Requests\UpdateideaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $ideas = Auth::user()
            ->ideas()
            ->get();

        return view('ideas.index', [
            'ideas' => $ideas
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
            'idea' => $idea
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea): View
    {
        //
    }
}
