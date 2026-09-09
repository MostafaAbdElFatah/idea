<?php

declare(strict_types=1);

use App\Models\User;

covers(User::class);

describe('User model configuration', function (): void {
    it('only allows name, email and password to be mass assigned', function (): void {
        expect((new User)->getFillable())->toBe(['name', 'email', 'password']);
    });

    it('hides the password and remember token', function (): void {
        expect((new User)->getHidden())->toBe(['password', 'remember_token']);
    });

    it('omits hidden attributes from array and JSON output', function (): void {
        $user = new User(['email' => 'jane@example.com', 'password' => 'secret', 'remember_token' => 'token']);

        expect($user->toArray())->toHaveKey('email')
            ->not->toHaveKey('password')
            ->not->toHaveKey('remember_token')
            ->and($user->toJson())->not->toContain('secret');
    });

    it('hashes the password on assignment', function (): void {
        $user = new User(['password' => 'plain-text']);

        expect($user->password)->not->toBe('plain-text')
            ->and(password_verify('plain-text', $user->password))->toBeTrue();
    });

    it('casts email_verified_at to a datetime', function (): void {
        expect((new User)->getCasts())->toHaveKey('email_verified_at', 'datetime')
            ->toHaveKey('password', 'hashed');
    });

    it('declares foreign keys for ideas and steps', function (): void {
        $user = new User;

        expect($user->ideas()->getForeignKeyName())->toBe('user_id')
            ->and($user->steps()->getForeignKeyName())->toBe('user_id');
    });
})->group('unit', 'models');
