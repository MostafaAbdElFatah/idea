<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\SessionsController;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

covers(SessionsController::class);

describe('logout', function (): void {
    it('logs out an authenticated user', function (): void {
        actingAs(User::factory()->create());

        delete(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('success', 'You have been logged out.');

        $this->assertGuest();
    });

    it('redirects guests to the login page', function (): void {
        delete(route('logout'))->assertRedirect(route('login'));
    });

    it('is not reachable with GET', function (): void {
        actingAs(User::factory()->create());

        $this->get('/logout')->assertMethodNotAllowed();
    });
})->group('feature', 'controllers');
