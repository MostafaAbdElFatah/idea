<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\User;
use App\Policies\IdeaPolicy;
use Illuminate\Support\Facades\Gate;

covers(IdeaPolicy::class);

describe('IdeaPolicy integration', function (): void {
    it('is auto-discovered for the Idea model', function (): void {
        expect(Gate::getPolicyFor(Idea::class))->toBeInstanceOf(IdeaPolicy::class);
    });

    it('denies class abilities through the gate', function (string $ability): void {
        $user = User::factory()->create();

        expect(Gate::forUser($user)->denies($ability, Idea::class))->toBeTrue();
    })->with('class abilities');

    it('denies model abilities through the gate for the owner and others', function (string $ability): void {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $idea = Idea::factory()->for($owner)->create();

        expect(Gate::forUser($owner)->denies($ability, $idea))->toBeTrue()
            ->and(Gate::forUser($other)->denies($ability, $idea))->toBeTrue();
    })->with('model abilities');

    it('denies guests', function (): void {
        $idea = Idea::factory()->create();

        expect(Gate::denies('view', $idea))->toBeTrue()
            ->and(Gate::denies('create', Idea::class))->toBeTrue();
    });
})->group('feature', 'policies');
