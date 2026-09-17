<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;

class ProfilePasswordController extends Controller
{
    /**
     * Change the authenticated user's password.
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => $request->validated('password')]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Password changed successfully.');
    }
}
