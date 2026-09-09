<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

covers(User::class);

describe('User relationships', function (): void {
    it('has many ideas', function (): void {
        $user = User::factory()->create();
        Idea::factory()->count(2)->for($user)->create();
        Idea::factory()->create();

        expect($user->ideas())->toBeInstanceOf(HasMany::class)
            ->and($user->load('ideas')->ideas)->toHaveCount(2);
    });

    it('declares a steps relationship', function (): void {
        expect(User::factory()->create()->steps())->toBeInstanceOf(HasMany::class);
    });
})->group('feature', 'models');

describe('User persistence', function (): void {
    it('stores a hashed password', function (): void {
        $user = User::factory()->create(['password' => 'jane-password']);

        expect($user->fresh()->password)->not->toBe('jane-password')
            ->and(Hash::check('jane-password', $user->fresh()->password))->toBeTrue();
    });

    it('casts email_verified_at to a Carbon instance', function (): void {
        $this->travelTo('2026-01-02 03:04:05');
        $user = User::factory()->create();

        expect($user->fresh()->email_verified_at)->toBeInstanceOf(CarbonInterface::class)
            ->and($user->fresh()->email_verified_at->toDateTimeString())->toBe('2026-01-02 03:04:05');
    });

    it('supports the unverified factory state', function (): void {
        expect(User::factory()->unverified()->create()->fresh()->email_verified_at)->toBeNull();
    });

    it('never serialises the password or remember token', function (): void {
        $json = User::factory()->create()->fresh()->toJson();

        expect($json)->not->toContain('password')->not->toContain('remember_token');
    });
})->group('feature', 'models');
