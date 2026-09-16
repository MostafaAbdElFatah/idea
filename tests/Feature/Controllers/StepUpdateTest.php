<?php

declare(strict_types=1);

use App\Http\Controllers\Ideas\StepController;
use App\Models\Idea;
use App\Models\Step;

use function Pest\Laravel\from;
use function Pest\Laravel\patch;

covers(StepController::class);

describe('updating a step', function (): void {
    it('toggles the completed flag and redirects back', function (string $state, bool $expected): void {
        $step = Step::factory()->{$state}()->create();
        loginAs($step->idea->user);

        from(route('idea.show', $step->idea_id))
            ->patch(route('steps.update', $step))
            ->assertRedirect(route('idea.show', $step->idea_id))
            ->assertSessionHas('success', 'Step updated successfully.');

        expect((bool) $step->fresh()->completed)->toBe($expected);
    })->with([
        'incomplete to completed' => ['incomplete', true],
        'completed to incomplete' => ['completed', false],
    ]);

    it('only changes the targeted step', function (): void {
        $idea = Idea::factory()->create();
        [$target, $other] = Step::factory()->for($idea)->incomplete()->count(2)->create();
        loginAs($idea->user);

        patch(route('steps.update', $target))->assertRedirect();

        expect((bool) $other->fresh()->completed)->toBeFalse();
    });

    it('hides steps from users who do not own the idea', function (): void {
        $step = Step::factory()->incomplete()->create();
        loginAs();

        patch(route('steps.update', $step))->assertNotFound();

        expect((bool) $step->fresh()->completed)->toBeFalse();
    });

    it('returns 404 for a missing step', function (): void {
        loginAs();

        patch(route('steps.update', 999))->assertNotFound();
    });

    it('requires authentication', function (): void {
        $step = Step::factory()->incomplete()->create();

        patch(route('steps.update', $step))->assertRedirect(route('login'));

        expect((bool) $step->fresh()->completed)->toBeFalse();
    });
})->group('feature', 'controllers');
