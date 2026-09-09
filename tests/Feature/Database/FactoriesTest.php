<?php

declare(strict_types=1);

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\Step;
use App\Models\User;

describe('factories', function (): void {
    it('creates users', function (): void {
        User::factory()->count(3)->create();

        $this->assertDatabaseCount('users', 3);
    });

    it('creates ideas with their own user', function (): void {
        Idea::factory()->count(3)->create();

        $this->assertDatabaseCount('ideas', 3);
        $this->assertDatabaseCount('users', 3);
        expect(Idea::query()->whereNull('user_id')->count())->toBe(0);
    });

    it('creates steps with their own idea', function (): void {
        Step::factory()->count(3)->create();

        $this->assertDatabaseCount('steps', 3);
        $this->assertDatabaseCount('ideas', 3);
        expect(Step::query()->whereNull('idea_id')->count())->toBe(0);
    });

    it('applies idea status states', function (): void {
        expect(Idea::factory()->pending()->create()->status)->toBe(IdeaStatus::PENDING)
            ->and(Idea::factory()->completed()->create()->status)->toBe(IdeaStatus::COMPLETED)
            ->and(Idea::factory()->archived()->create()->status)->toBe(IdeaStatus::ARCHIVED)
            ->and(Idea::factory()->withStatus(IdeaStatus::DRAFT)->create()->status)->toBe(IdeaStatus::DRAFT);
    });

    it('applies idea link states', function (): void {
        expect(Idea::factory()->withoutLinks()->create()->links->getArrayCopy())->toBe([])
            ->and(Idea::factory()->create()->links->getArrayCopy())->toHaveCount(1);
    });

    it('applies step completion states', function (): void {
        expect((bool) Step::factory()->completed()->create()->completed)->toBeTrue()
            ->and((bool) Step::factory()->incomplete()->create()->completed)->toBeFalse();
    });

    it('applies the unverified user state', function (): void {
        expect(User::factory()->unverified()->create()->email_verified_at)->toBeNull()
            ->and(User::factory()->create()->email_verified_at)->not->toBeNull();
    });

    it('generates unique emails', function (): void {
        User::factory()->count(20)->create();

        expect(User::query()->distinct()->count('email'))->toBe(20);
    });
})->group('feature', 'database');
