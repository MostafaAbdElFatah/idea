<?php

declare(strict_types=1);

use App\Enums\IdeaStatus;
use App\Http\Controllers\Ideas\IdeaController;
use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\post;

covers(IdeaController::class);

describe('creating an idea', function (): void {
    it('stores the idea for the authenticated user and redirects to it', function (): void {
        $user = loginAs();

        $response = post(route('idea.store'), [
            'title' => 'Build a garden',
            'description' => 'Raised beds in the backyard',
            'status' => IdeaStatus::PENDING->value,
            'links' => ['https://example.com'],
        ]);

        $idea = Idea::query()->sole();

        $response->assertRedirect(route('idea.show', $idea))
            ->assertSessionHas('success', 'Idea created successfully.');

        expect($idea->user_id)->toBe($user->id)
            ->and($idea->title)->toBe('Build a garden')
            ->and($idea->description)->toBe('Raised beds in the backyard')
            ->and($idea->status)->toBe(IdeaStatus::PENDING)
            ->and($idea->links->getArrayCopy())->toBe(['https://example.com']);
    });

    it('defaults the status to pending and links to empty', function (): void {
        loginAs();

        post(route('idea.store'), ['title' => 'Minimal idea'])->assertRedirect();

        $idea = Idea::query()->sole();

        expect($idea->status)->toBe(IdeaStatus::PENDING)
            ->and($idea->links->getArrayCopy())->toBe([]);
    });

    it('defaults the status to pending when an empty status is submitted', function (): void {
        loginAs();

        post(route('idea.store'), ['title' => 'Minimal idea', 'status' => ''])->assertRedirect();

        expect(Idea::query()->sole()->status)->toBe(IdeaStatus::PENDING);
    });

    it('stores the uploaded image on the public disk', function (): void {
        Storage::fake('public');
        loginAs();

        post(route('idea.store'), [
            'title' => 'Build a garden',
            'image' => UploadedFile::fake()->image('garden.jpg'),
        ])->assertRedirect();

        $idea = Idea::query()->sole();

        expect($idea->image_path)->toStartWith('ideas/');
        Storage::disk('public')->assertExists($idea->image_path);
    });

    it('rejects invalid input and reopens the dialog', function (array $payload, string $field): void {
        loginAs();

        post(route('idea.store'), $payload)
            ->assertSessionHasErrors($field)
            ->assertSessionHas('open_modal', 'create-idea');

        expect(Idea::query()->count())->toBe(0);
    })->with([
        'missing title' => [[], 'title'],
        'short title' => [['title' => 'ab'], 'title'],
        'unknown status' => [['title' => 'Valid', 'status' => 'nope'], 'status'],
        'invalid link' => [['title' => 'Valid', 'links' => ['not-a-url']], 'links.0'],
    ]);

    it('keeps the selected status after a failed submission', function (): void {
        loginAs();

        $this->from(route('idea.index'))
            ->followingRedirects()
            ->post(route('idea.store'), ['status' => IdeaStatus::COMPLETED->value])
            ->assertOk()
            ->assertSee('value: \''.IdeaStatus::COMPLETED->value.'\'', false);
    });

    it('requires authentication', function (): void {
        post(route('idea.store'), ['title' => 'Guest idea'])->assertRedirect(route('login'));

        expect(Idea::query()->count())->toBe(0);
    });
})->group('feature', 'controllers');

describe('creating steps with an idea', function (): void {
    it('creates a step for each submitted description', function (): void {
        loginAs();

        post(route('idea.store'), [
            'title' => 'Learn guitar',
            'steps' => ['Buy a guitar', 'Find a teacher'],
        ])->assertRedirect();

        $idea = Idea::query()->with('steps')->sole();

        expect($idea->steps->pluck('description')->all())->toBe(['Buy a guitar', 'Find a teacher'])
            ->and($idea->steps->every(fn ($step): bool => ! $step->completed))->toBeTrue();
    });

    it('trims step descriptions', function (): void {
        loginAs();

        post(route('idea.store'), [
            'title' => 'Learn guitar',
            'steps' => ['  Practice chords  '],
        ])->assertRedirect();

        expect(Idea::query()->sole()->steps()->pluck('description')->all())->toBe(['Practice chords']);
    });

    it('rejects blank steps', function (): void {
        loginAs();

        post(route('idea.store'), [
            'title' => 'Learn guitar',
            'steps' => ['Practice chords', '   '],
        ])->assertSessionHasErrors('steps.1');

        expect(Idea::query()->count())->toBe(0);
    });

    it('rejects steps longer than 255 characters', function (): void {
        loginAs();

        post(route('idea.store'), [
            'title' => 'Learn guitar',
            'steps' => [str_repeat('a', 256)],
        ])->assertSessionHasErrors('steps.0');

        expect(Idea::query()->count())->toBe(0);
    });
})->group('feature', 'controllers');

describe('viewing and deleting an idea', function (): void {
    it('lets the owner view and delete their idea', function (): void {
        $user = loginAs();
        $idea = Idea::factory()->for($user)->create();

        $this->get(route('idea.show', $idea))->assertOk();
        $this->delete(route('idea.delete', $idea))->assertRedirect(route('idea.index'));

        expect(Idea::query()->count())->toBe(0);
    });

    it('hides an idea from other users', function (): void {
        $idea = Idea::factory()->create();
        loginAs();

        $this->get(route('idea.show', $idea))->assertNotFound();
        $this->delete(route('idea.delete', $idea))->assertNotFound();

        expect($idea->fresh())->not->toBeNull();
    });
})->group('feature', 'controllers');
