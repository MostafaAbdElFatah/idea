<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Store a newly registered user.
     */
    public function store(Request $request)
    {
        // Process registration here

        return redirect()
            ->route('login')
            ->with('success', 'Account created successfully.');
    }
}
