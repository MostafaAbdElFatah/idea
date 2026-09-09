<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\User;
use App\Policies\IdeaPolicy;

covers(IdeaPolicy::class);

describe('IdeaPolicy', function (): void {
    it('denies class-level abilities', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);

        expect((new IdeaPolicy)->{$ability}($user))->toBeFalse();
    })->with('class abilities');

    it('denies model-level abilities for the owner', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);
        $idea = Idea::factory()->make(['id' => 10, 'user_id' => 1]);

        expect((new IdeaPolicy)->{$ability}($user, $idea))->toBeFalse();
    })->with('model abilities');

    it('denies model-level abilities for another user', function (string $ability): void {
        $user = User::factory()->make(['id' => 2]);
        $idea = Idea::factory()->make(['id' => 10, 'user_id' => 1]);

        expect((new IdeaPolicy)->{$ability}($user, $idea))->toBeFalse();
    })->with('model abilities');
})->group('unit', 'policies');
