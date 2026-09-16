<?php

declare(strict_types=1);

use App\Models\User;

describe('authentication pages', function (): void {
    it('render the login page without console errors', function (): void {
        visit(route('login'))->assertNoSmoke()->assertSee('Email');
    });

    it('render the register page without console errors', function (): void {
        visit(route('register'))
            ->assertNoSmoke()
            ->assertSee('First name')
            ->assertSee('Last name');
    });

    it('render the register page on mobile', function (): void {
        visit(route('register'))->on()->mobile()->assertNoSmoke()->assertSee('First name');
    });
})->group('browser');

describe('authentication flow', function (): void {
    it('lets a visitor register from the form', function (): void {
        visit(route('register'))
            ->fill('first_name', 'Jane')
            ->fill('last_name', 'Doe')
            ->fill('email', 'jane@example.com')
            ->fill('password', 'password123')
            ->fill('password_confirmation', 'password123')
            ->click('create Account')
            ->assertPathIs('/ideas')
            ->assertNoJavascriptErrors();

        expect(User::where('email', 'jane@example.com')->exists())->toBeTrue();
    });

    it('shows an error for invalid login credentials', function (): void {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        visit(route('login'))
            ->fill('email', 'jane@example.com')
            ->fill('password', 'wrong-password')
            ->click('Sign In')
            ->assertPathIs('/login')
            ->assertSee('These credentials do not match our records.');
    });

    it('lets an existing user log in', function (): void {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        visit(route('login'))
            ->fill('email', 'jane@example.com')
            ->fill('password', 'password123')
            ->click('Sign In')
            ->assertPathIs('/ideas')
            ->assertNoJavascriptErrors();
    });
})->group('browser');
