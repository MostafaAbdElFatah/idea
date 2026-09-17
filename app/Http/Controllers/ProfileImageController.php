<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileImageController extends Controller
{
    /**
     * Remove the authenticated user's profile image.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_image_path !== null) {
            Storage::disk('public')->delete($user->profile_image_path);

            $user->update(['profile_image_path' => null]);
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile photo removed.');
    }
}
