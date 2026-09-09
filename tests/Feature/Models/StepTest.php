<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\Step;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

covers(Step::class);

describe('Step relationships', function (): void {
    it('belongs to an idea', function (): void {
        $idea = Idea::factory()->create();
        $step = Step::factory()->for($idea)->create();

        expect($step->idea())->toBeInstanceOf(BelongsTo::class)
            ->and($step->load('idea')->idea->is($idea))->toBeTrue();
    });
})->group('feature', 'models');

describe('Step persistence', function (): void {
    it('stores the completed flag', function (bool $completed): void {
        $step = Step::factory()->create(['completed' => $completed]);

        $this->assertDatabaseHas('steps', ['id' => $step->id, 'completed' => $completed ? 1 : 0]);
        expect((bool) $step->fresh()->completed)->toBe($completed);
    })->with(['completed' => [true], 'incomplete' => [false]]);

    it('defaults completed to false at the database level', function (): void {
        $idea = Idea::factory()->create();
        $step = Step::query()->create(['idea_id' => $idea->id, 'description' => 'Bare step']);

        expect((bool) $step->fresh()->completed)->toBeFalse();
    });
})->group('feature', 'models');
