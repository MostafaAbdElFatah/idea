<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmailRequest;
use Illuminate\Http\RedirectResponse;

class ProfileEmailController extends Controller
{
    /**
     * Change the authenticated user's email address.
     */
    public function update(UpdateEmailRequest $request): RedirectResponse
    {
        $request->user()->update(['email' => $request->validated('email')]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Email changed successfully.');
    }
}
