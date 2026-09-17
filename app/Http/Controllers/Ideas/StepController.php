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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Step $step): RedirectResponse
    {
        Gate::authorize('update', $step);

        $step->update(['completed' => ! $step->completed]);

        return back()
            ->with('success', 'Step updated successfully.');
    }
}
