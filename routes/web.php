<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SessionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register.store');
Route::get('/login', [SessionsController::class, 'create'])
    ->name('login');
Route::post('/login', [SessionsController::class, 'store'])
    ->name('login.store');

Route::delete('/logout', [SessionsController::class, 'destroy'])
    ->name('logout');
