<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBannerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileBannerController extends Controller
{
    /**
     * Replace the authenticated user's profile banner.
     */
    public function update(UpdateBannerRequest $request): RedirectResponse
    {
        $user = $request->user();
        $previousPath = $user->banner_image_path;

        $user->update(['banner_image_path' => $request->file('banner_image')->store('profile-banners', 'public')]);

        if ($previousPath !== null) {
            Storage::disk('public')->delete($previousPath);
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Cover image updated.');
    }

    /**
     * Remove the authenticated user's profile banner.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->banner_image_path !== null) {
            Storage::disk('public')->delete($user->banner_image_path);

            $user->update(['banner_image_path' => null]);
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Cover image removed.');
    }
}
