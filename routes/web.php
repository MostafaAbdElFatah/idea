<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SessionsController;
use App\Http\Controllers\Ideas\IdeaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register.store');
    Route::get('/login', [SessionsController::class, 'create'])
        ->name('login');
    Route::post('/login', [SessionsController::class, 'store'])
        ->name('login.store');
});

Route::middleware('auth')->group(function () {

    Route::delete('/logout', [SessionsController::class, 'destroy'])
        ->name('logout');

    Route::redirect('/', '/ideas')
        ->name('home');

    Route::get('/ideas', [IdeaController::class, 'index'])
        ->name('idea.index');
    Route::post('/ideas', [IdeaController::class, 'store'])
        ->name('idea.store');

    Route::get('/ideas/{idea}', [IdeaController::class, 'show'])
        ->name('idea.show');
    Route::patch('/ideas/{idea}', [IdeaController::class, 'show'])
        ->name('idea.edit');
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])
        ->name('idea.delete');
});
