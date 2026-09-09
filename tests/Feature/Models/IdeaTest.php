<?php

declare(strict_types=1);

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

covers(Idea::class);

describe('Idea relationships', function (): void {
    it('belongs to a user', function (): void {
        $user = User::factory()->create();
        $idea = Idea::factory()->for($user)->create();

        expect($idea->user())->toBeInstanceOf(BelongsTo::class)
            ->and($idea->load('user')->user->is($user))->toBeTrue()
            ->and($user->load('ideas')->ideas->first()->is($idea))->toBeTrue();
    });

    it('has many steps', function (): void {
        $idea = Idea::factory()->create();
        Step::factory()->count(3)->for($idea)->create();
        Step::factory()->create();

        expect($idea->steps())->toBeInstanceOf(HasMany::class)
            ->and($idea->load('steps')->steps)->toHaveCount(3);
    });

    it('eager loads steps without lazy loading violations', function (): void {
        Idea::factory()->count(2)->has(Step::factory()->count(2))->create();

        $ideas = Idea::with('steps')->get();

        expect($ideas->sum(fn (Idea $idea): int => $idea->steps->count()))->toBe(4);
    });
})->group('feature', 'models');

describe('Idea persistence', function (): void {
    it('round-trips the status enum through the database', function (IdeaStatus $status, string $value): void {
        $idea = Idea::factory()->withStatus($status)->create();

        expect($idea->fresh()->status)->toBe($status);
        //$this->assertDatabaseHas('ideas', ['id' => $idea->id, 'status' => $value]);
        expect(Idea::query()->whereKey($idea->id)->where('status', $value)->exists())->toBeTrue();
    })->with('idea statuses');

    it('stores pending when no status is given', function (): void {
        $user = User::factory()->create();
        $idea = Idea::query()->create(['user_id' => $user->id, 'title' => 'Untitled']);

        $this->assertDatabaseHas('ideas', ['id' => $idea->id, 'status' => 'pending']);
        expect($idea->fresh()->status)->toBe(IdeaStatus::PENDING);
    });

    it('round-trips links as JSON', function (): void {
        $idea = Idea::factory()->create(['links' => ['https://a.test', 'https://b.test']]);

        expect($idea->fresh()->links->getArrayCopy())->toBe(['https://a.test', 'https://b.test']);
        $this->assertDatabaseHas('ideas', ['id' => $idea->id, 'links' => '["https:\/\/a.test","https:\/\/b.test"]']);
    });

    it('persists an empty links list', function (): void {
        $idea = Idea::factory()->withoutLinks()->create();

        expect($idea->fresh()->links->getArrayCopy())->toBe([]);
    });

    it('allows mutating links through the array object', function (): void {
        $idea = Idea::factory()->withoutLinks()->create();
        $idea->links[] = 'https://added.test';
        $idea->save();

        expect($idea->fresh()->links->getArrayCopy())->toBe(['https://added.test']);
    });

    it('allows a null description and image path', function (): void {
        $idea = Idea::factory()->create(['description' => null, 'image_path' => null]);

        expect($idea->fresh())->description->toBeNull()->image_path->toBeNull();
    });
})->group('feature', 'models');
