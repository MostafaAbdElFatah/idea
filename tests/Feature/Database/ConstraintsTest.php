<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Illuminate\Database\QueryException;

describe('database constraints', function (): void {
    it('rejects duplicate user emails', function (): void {
        User::factory()->create(['email' => 'jane@example.com']);

        expect(fn () => User::factory()->create(['email' => 'jane@example.com']))
            ->toThrow(QueryException::class);
    });

    it('rejects an idea for a missing user', function (): void {
        expect(fn () => Idea::factory()->create(['user_id' => 999]))->toThrow(QueryException::class);
    });

    it('rejects a step for a missing idea', function (): void {
        expect(fn () => Step::factory()->create(['idea_id' => 999]))->toThrow(QueryException::class);
    });

    it('cascades user deletion to ideas and steps', function (): void {
        $user = User::factory()->create();
        $idea = Idea::factory()->for($user)->create();
        Step::factory()->count(2)->for($idea)->create();
        $unrelated = Step::factory()->create();

        $user->delete();

        $this->assertDatabaseMissing('ideas', ['id' => $idea->id]);
        $this->assertDatabaseCount('steps', 1);
        $this->assertDatabaseHas('steps', ['id' => $unrelated->id]);
    });

    it('cascades idea deletion to steps', function (): void {
        $idea = Idea::factory()->has(Step::factory()->count(3))->create();

        $idea->delete();

        $this->assertDatabaseCount('steps', 0);
        $this->assertDatabaseCount('users', 1);
    });

    it('rejects an idea without a title', function (): void {
        $user = User::factory()->create();

        expect(fn () => Idea::query()->create(['user_id' => $user->id, 'title' => null]))
            ->toThrow(QueryException::class);
    });

    it('rejects a step without a description', function (): void {
        $idea = Idea::factory()->create();

        expect(fn () => Step::query()->create(['idea_id' => $idea->id, 'description' => null]))
            ->toThrow(QueryException::class);
    });
})->group('feature', 'database');
