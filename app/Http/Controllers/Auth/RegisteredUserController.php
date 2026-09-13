<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegisterRequest;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Store a newly registered user.
     */
    public function store(StoreRegisterRequest $request)
    {
        $user = User::create($request->validated());

        Auth::login($user);

        return redirect()
            ->route('home')
            ->with('success', 'Account created successfully.');
    }
}
