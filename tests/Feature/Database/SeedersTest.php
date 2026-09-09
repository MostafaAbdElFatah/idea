<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Database\Seeders\UserSeeder;

describe('seeders', function (): void {
    it('seeds the reference users', function (): void {
        $this->seed(UserSeeder::class);

        $this->assertDatabaseCount('users', 10);
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com', 'first_name' => 'Admin']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    });

    it('is idempotent for users', function (): void {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertDatabaseCount('users', 10);
        expect(User::query()->where('email', 'admin@example.com')->count())->toBe(1);
    });

    it('seeds the full data set with every idea and step attached', function (): void {
        $this->seed();

        $this->assertDatabaseCount('users', 10);
        $this->assertDatabaseCount('ideas', 1000);
        $this->assertDatabaseCount('steps', 3000);

        expect(Idea::query()->whereNull('user_id')->count())->toBe(0)
            ->and(Step::query()->whereNull('idea_id')->count())->toBe(0)
            ->and(Idea::query()->where('user_id', 1)->count())->toBe(0)
            ->and(Idea::query()->where('user_id', 2)->count())->toBeGreaterThanOrEqual(200)
            ->and(Idea::query()->where('user_id', 3)->count())->toBeGreaterThanOrEqual(200);
    });
})->group('feature', 'database');
