<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SessionsController;
use App\Http\Controllers\Ideas\IdeaController;
use App\Http\Controllers\Ideas\IdeaImageController;
use App\Http\Controllers\Ideas\StepController;
use App\Http\Controllers\ProfileBannerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileEmailController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\ProfilePasswordController;
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

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile/image', [ProfileImageController::class, 'destroy'])
        ->name('profile.image.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    Route::put('/profile/password', [ProfilePasswordController::class, 'update'])
        ->name('profile.password.update');
    Route::put('/profile/email', [ProfileEmailController::class, 'update'])
        ->name('profile.email.update');
    Route::post('/profile/banner', [ProfileBannerController::class, 'update'])
        ->name('profile.banner.update');
    Route::delete('/profile/banner', [ProfileBannerController::class, 'destroy'])
        ->name('profile.banner.destroy');

    Route::get('/ideas', [IdeaController::class, 'index'])
        ->name('idea.index');
    Route::post('/ideas', [IdeaController::class, 'store'])
        ->name('idea.store');

    Route::get('/ideas/{idea}', [IdeaController::class, 'show'])
        ->name('idea.show');
    Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])
        ->name('idea.update');
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])
        ->name('idea.delete');
    Route::delete('/ideas/{idea}/image', [IdeaImageController::class, 'destroy'])
        ->name('idea.image.destroy');

    Route::patch('/steps/{step}', [StepController::class, 'update'])
        ->name('steps.update');
});
