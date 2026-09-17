<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ideas;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class IdeaImageController extends Controller
{
    /**
     * Remove the idea's image from storage.
     */
    public function destroy(Request $request, Idea $idea): RedirectResponse
    {
        Gate::authorize('update', $idea);

        if ($idea->image_path !== null) {
            Storage::disk('public')->delete($idea->image_path);

            $idea->update(['image_path' => null]);
        }

        $response = back()->with('success', 'Image removed successfully.');

        if ($request->boolean('reopen_dialog')) {
            $response->with('open_modal', 'edit-idea');
        }

        return $response;
    }
}
