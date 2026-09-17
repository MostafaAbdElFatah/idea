<?php

declare(strict_types=1);

use App\Enums\IdeaStatus;
use App\Http\Controllers\Ideas\IdeaController;
use App\Http\Requests\UpdateIdeaRequest;
use App\Models\Idea;

use function Pest\Laravel\from;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;

covers(IdeaController::class, UpdateIdeaRequest::class);

describe('updating an idea', function (): void {
    it('updates the idea for its owner and redirects to it', function (): void {
        $idea = Idea::factory()->for(loginAs())->create(['title' => 'Old title']);

        patch(route('idea.update', $idea), [
            'title' => 'New title',
            'status' => IdeaStatus::COMPLETED->value,
            'steps' => ['Buy a guitar'],
        ])
            ->assertRedirect(route('idea.show', $idea))
            ->assertSessionHas('success', 'Idea updated successfully.');

        $idea->refresh();

        expect($idea->title)->toBe('New title')
            ->and($idea->status)->toBe(IdeaStatus::COMPLETED)
            ->and($idea->steps()->pluck('description')->all())->toBe(['Buy a guitar']);
    });

    it('rejects invalid input and reopens the edit dialog', function (): void {
        $idea = Idea::factory()->for(loginAs())->create(['title' => 'Old title']);

        from(route('idea.show', $idea))
            ->patch(route('idea.update', $idea), ['title' => 'ab'])
            ->assertRedirect(route('idea.show', $idea))
            ->assertSessionHasErrors('title')
            ->assertSessionHas('open_modal', 'edit-idea');

        expect($idea->fresh()->title)->toBe('Old title');
    });

    it('hides the idea from other users', function (): void {
        $idea = Idea::factory()->create(['title' => 'Old title']);
        loginAs();

        patch(route('idea.update', $idea), ['title' => 'Hijacked'])->assertNotFound();

        expect($idea->fresh()->title)->toBe('Old title');
    });

    it('requires authentication', function (): void {
        $idea = Idea::factory()->create();

        patch(route('idea.update', $idea), ['title' => 'Guest edit'])->assertRedirect(route('login'));
    });

    it('renders the edit form as a PATCH to the update route', function (): void {
        $idea = Idea::factory()->for(loginAs())->create();

        get(route('idea.show', $idea))
            ->assertOk()
            ->assertSee('action="'.route('idea.update', $idea).'"', false)
            ->assertSee('name="_method" value="PATCH"', false);
    });
})->group('feature', 'controllers');
