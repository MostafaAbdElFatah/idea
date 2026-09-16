<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use App\Policies\StepPolicy;

covers(StepPolicy::class);

/**
 * Build an unsaved step whose idea belongs to the given user id.
 */
function stepOwnedBy(int $userId): Step
{
    $idea = Idea::factory()->make(['id' => 1, 'user_id' => $userId]);

    return Step::factory()->make(['id' => 5, 'idea_id' => 1])->setRelation('idea', $idea);
}

describe('StepPolicy', function (): void {
    it('allows class-level abilities', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);

        expect((new StepPolicy)->{$ability}($user))->toBeTrue();
    })->with('class abilities');

    it('allows the idea owner to view, update, and delete', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);

        expect(allowed((new StepPolicy)->{$ability}($user, stepOwnedBy(1))))->toBeTrue();
    })->with(['view', 'update', 'delete']);

    it('denies model-level abilities for another user', function (string $ability): void {
        $user = User::factory()->make(['id' => 2]);

        expect(allowed((new StepPolicy)->{$ability}($user, stepOwnedBy(1))))->toBeFalse();
    })->with('model abilities');

    it('denies restore and force delete for the owner', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);

        expect(allowed((new StepPolicy)->{$ability}($user, stepOwnedBy(1))))->toBeFalse();
    })->with(['restore', 'forceDelete']);
})->group('unit', 'policies');
