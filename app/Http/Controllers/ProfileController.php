<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteAccount;
use App\Actions\UpdateProfile;
use App\Enums\IdeaStatus;
use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Step;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $statusCounts = $user->statusCounts();

        return view('profile.show', [
            'user' => $user,
            'stats' => [
                'ideas' => $statusCounts->get('all', 0),
                'completed' => $statusCounts->get(IdeaStatus::COMPLETED->value, 0),
                'inProgress' => $statusCounts->get(IdeaStatus::IN_PROGRESS->value, 0),
                'stepsDone' => Step::query()
                    ->whereRelation('idea', 'user_id', $user->id)
                    ->where('completed', true)
                    ->count(),
            ],
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request, UpdateProfile $updateProfile): RedirectResponse
    {
        $updateProfile->handle($request->user(), $request->validated(), $request->file('profile_image'));

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the authenticated user's account and log them out.
     */
    public function destroy(DeleteAccountRequest $request, DeleteAccount $deleteAccount): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $deleteAccount->handle($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Your account has been deleted.');
    }
}
