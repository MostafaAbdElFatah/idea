<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\SessionsController;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

covers(SessionsController::class);

describe('login page', function (): void {
    it('renders for guests', function (): void {
        get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login');
    });

    it('redirects authenticated users away', function (): void {
        actingAs(User::factory()->create());

        get(route('login'))->assertRedirect(route('home'));
    });
})->group('feature', 'controllers');

describe('login', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);
    });

    it('logs in a user with valid credentials', function (): void {
        post(route('login.store'), [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('home'))
            ->assertSessionHas('success', 'You are now logged in.');

        $this->assertAuthenticatedAs($this->user);
    });

    it('trims surrounding whitespace from credentials', function (): void {
        post(route('login.store'), [
            'email' => '  jane@example.com  ',
            'password' => '  password123  ',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($this->user);
    });

    it('keeps a remembered session when remember is checked', function (): void {
        post(route('login.store'), [
            'email' => 'jane@example.com',
            'password' => 'password123',
            'remember' => 'on',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($this->user);
        expect($this->user->fresh()->remember_token)->not->toBeNull();
    });

    it('regenerates the session id to prevent fixation', function (): void {
        $this->get(route('login'));
        $previousId = session()->getId();

        post(route('login.store'), [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        expect(session()->getId())->not->toBe($previousId);
    });

    it('rejects an incorrect password', function (): void {
        post(route('login.store'), [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('rejects an unknown email', function (): void {
        post(route('login.store'), [
            'email' => 'nobody@example.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('does not flash the password back on failure', function (): void {
        post(route('login.store'), [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ])
            ->assertSessionHasInput('email', 'jane@example.com')
            ->assertSessionMissing('_old_input.password');
    });

    it('requires an email and a password', function (): void {
        post(route('login.store'), [])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    });

    it('rejects a malformed email', function (): void {
        post(route('login.store'), [
            'email' => 'not-an-email',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');
    });
})->group('feature', 'controllers');
