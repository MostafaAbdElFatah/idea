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

    it('denies class abilities through the gate', function (string $ability): void {
        $user = User::factory()->create();

        expect(Gate::forUser($user)->denies($ability, Step::class))->toBeTrue();
    })->with('class abilities');

    it('denies model abilities through the gate', function (string $ability): void {
        $user = User::factory()->create();
        $step = Step::factory()->create();

        expect(Gate::forUser($user)->denies($ability, $step))->toBeTrue();
    })->with('model abilities');

    it('denies guests', function (): void {
        expect(Gate::denies('view', Step::factory()->create()))->toBeTrue();
    });
})->group('feature', 'policies');
