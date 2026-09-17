<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegisterRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Store a newly registered user.
     */
    public function store(StoreRegisterRequest $request)
    {
        $user = User::create([
            ...$request->safe()->except('profile_image'),
            'profile_image_path' => $request->file('profile_image')?->store('profile-images', 'public'),
        ]);

        Auth::login($user, $request->boolean('remember'));

        return redirect()
            ->route('home')
            ->with('success', 'Account created successfully.');
    }
}
