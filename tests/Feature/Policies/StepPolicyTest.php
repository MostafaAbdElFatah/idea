<?php

declare(strict_types=1);

use App\Models\Step;
use App\Models\User;
use App\Policies\StepPolicy;
use Illuminate\Support\Facades\Gate;

covers(StepPolicy::class);

describe('StepPolicy integration', function (): void {
    it('is auto-discovered for the Step model', function (): void {
        expect(Gate::getPolicyFor(Step::class))->toBeInstanceOf(StepPolicy::class);
    });

    it('allows class abilities through the gate', function (string $ability): void {
        $user = User::factory()->create();

        expect(Gate::forUser($user)->allows($ability, Step::class))->toBeTrue();
    })->with('class abilities');

    it('allows the idea owner and denies others through the gate', function (string $ability): void {
        $step = Step::factory()->create();
        $other = User::factory()->create();

        expect(Gate::forUser($step->idea->user)->allows($ability, $step))->toBeTrue()
            ->and(Gate::forUser($other)->denies($ability, $step))->toBeTrue();
    })->with(['view', 'update', 'delete']);

    it('denies restore and force delete through the gate', function (string $ability): void {
        $step = Step::factory()->create();

        expect(Gate::forUser($step->idea->user)->denies($ability, $step))->toBeTrue();
    })->with(['restore', 'forceDelete']);

    it('denies guests', function (): void {
        expect(Gate::denies('view', Step::factory()->create()))->toBeTrue();
    });
})->group('feature', 'policies');
