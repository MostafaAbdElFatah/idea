<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\User;
use App\Policies\IdeaPolicy;

covers(IdeaPolicy::class);

describe('IdeaPolicy', function (): void {
    it('allows class-level abilities', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);

        expect((new IdeaPolicy)->{$ability}($user))->toBeTrue();
    })->with('class abilities');

    it('allows the owner to view, update, and delete', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);
        $idea = Idea::factory()->make(['id' => 10, 'user_id' => 1]);

        expect(allowed((new IdeaPolicy)->{$ability}($user, $idea)))->toBeTrue();
    })->with(['view', 'update', 'delete']);

    it('denies model-level abilities for another user', function (string $ability): void {
        $user = User::factory()->make(['id' => 2]);
        $idea = Idea::factory()->make(['id' => 10, 'user_id' => 1]);

        expect(allowed((new IdeaPolicy)->{$ability}($user, $idea)))->toBeFalse();
    })->with('model abilities');

    it('denies restore and force delete for the owner', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);
        $idea = Idea::factory()->make(['id' => 10, 'user_id' => 1]);

        expect(allowed((new IdeaPolicy)->{$ability}($user, $idea)))->toBeFalse();
    })->with(['restore', 'forceDelete']);
})->group('unit', 'policies');
