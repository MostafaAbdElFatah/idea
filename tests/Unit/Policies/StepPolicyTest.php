<?php

declare(strict_types=1);

use App\Models\Step;
use App\Models\User;
use App\Policies\StepPolicy;

covers(StepPolicy::class);

describe('StepPolicy', function (): void {
    it('denies class-level abilities', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);

        expect((new StepPolicy)->{$ability}($user))->toBeFalse();
    })->with('class abilities');

    it('denies model-level abilities for any user', function (string $ability): void {
        $user = User::factory()->make(['id' => 1]);
        $step = Step::factory()->make(['id' => 5, 'idea_id' => 1]);

        expect((new StepPolicy)->{$ability}($user, $step))->toBeFalse();
    })->with('model abilities');
})->group('unit', 'policies');
