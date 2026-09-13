<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

covers(RegisteredUserController::class);

/**
 * @return array{first_name: string, last_name: string, email: string, password: string, password_confirmation: string}
 */
function validRegistration(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides);
}

describe('registration page', function (): void {
    it('renders for guests', function (): void {
        get(route('register'))
            ->assertOk()
            ->assertViewIs('auth.register');
    });

    it('redirects authenticated users away', function (): void {
        actingAs(User::factory()->create());

        get(route('register'))->assertRedirect(route('home'));
    });
})->group('feature', 'controllers');

describe('registration', function (): void {
    it('creates and logs in a user with valid data', function (): void {
        $response = post(route('register.store'), validRegistration());

        $response->assertRedirect(route('home'))
            ->assertSessionHas('success', 'Account created successfully.');

        $user = User::firstWhere('email', 'jane@example.com');

        expect($user)->not->toBeNull()
            ->and($user->first_name)->toBe('Jane')
            ->and($user->last_name)->toBe('Doe');

        $this->assertAuthenticatedAs($user);
    });

    it('hashes the stored password', function (): void {
        post(route('register.store'), validRegistration());

        $user = User::firstWhere('email', 'jane@example.com');

        expect($user->password)->not->toBe('password123')
            ->and(Hash::check('password123', $user->password))->toBeTrue();
    });

    it('trims surrounding whitespace before validating', function (): void {
        post(route('register.store'), validRegistration([
            'first_name' => '  Jane  ',
            'last_name' => "\tDoe\n",
            'email' => '  jane@example.com  ',
        ]))->assertRedirect(route('home'));

        $user = User::firstWhere('email', 'jane@example.com');

        expect($user)->not->toBeNull()
            ->and($user->first_name)->toBe('Jane')
            ->and($user->last_name)->toBe('Doe');
    });

    it('rejects authenticated users from registering again', function (): void {
        actingAs(User::factory()->create());

        post(route('register.store'), validRegistration())->assertRedirect(route('home'));

        expect(User::where('email', 'jane@example.com')->exists())->toBeFalse();
    });
})->group('feature', 'controllers');

describe('registration validation', function (): void {
    it('requires the given field', function (string $field): void {
        post(route('register.store'), validRegistration([$field => '']))
            ->assertSessionHasErrors($field);

        expect(User::count())->toBe(0);
    })->with(['first_name', 'last_name', 'email', 'password']);

    it('rejects names shorter than three characters', function (string $field): void {
        post(route('register.store'), validRegistration([$field => 'ab']))
            ->assertSessionHasErrors($field);
    })->with(['first_name', 'last_name']);

    it('rejects names longer than 255 characters', function (string $field): void {
        post(route('register.store'), validRegistration([$field => str_repeat('a', 256)]))
            ->assertSessionHasErrors($field);
    })->with(['first_name', 'last_name']);

    it('rejects invalid email addresses', function (string $email): void {
        post(route('register.store'), validRegistration(['email' => $email]))
            ->assertSessionHasErrors('email');
    })->with('invalid emails');

    it('rejects a duplicate email', function (): void {
        User::factory()->create(['email' => 'jane@example.com']);

        post(route('register.store'), validRegistration())
            ->assertSessionHasErrors('email');

        expect(User::where('email', 'jane@example.com')->count())->toBe(1);
    });

    it('rejects an unconfirmed password', function (): void {
        post(route('register.store'), validRegistration(['password_confirmation' => 'different123']))
            ->assertSessionHasErrors('password');

        expect(User::count())->toBe(0);
    });

    it('rejects a password shorter than eight characters', function (): void {
        post(route('register.store'), validRegistration([
            'password' => 'short',
            'password_confirmation' => 'short',
        ]))->assertSessionHasErrors('password');
    });
})->group('feature', 'controllers');
