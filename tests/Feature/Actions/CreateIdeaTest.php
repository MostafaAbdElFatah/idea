<?php

declare(strict_types=1);

use App\Actions\CreateIdea;
use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

covers(CreateIdea::class);

describe('CreateIdea action', function (): void {
    it('creates an idea with its steps for the user', function (): void {
        $user = User::factory()->create();

        $idea = app(CreateIdea::class)->handle($user, [
            'title' => 'Learn guitar',
            'description' => 'Play a song by summer',
            'status' => IdeaStatus::ACTIVE->value,
            'links' => ['https://example.com'],
            'steps' => ['Buy a guitar', 'Find a teacher'],
        ]);

        $idea->refresh()->load('steps');

        expect($idea->user_id)->toBe($user->id)
            ->and($idea->title)->toBe('Learn guitar')
            ->and($idea->description)->toBe('Play a song by summer')
            ->and($idea->status)->toBe(IdeaStatus::ACTIVE)
            ->and($idea->links->getArrayCopy())->toBe(['https://example.com'])
            ->and($idea->image_path)->toBeNull()
            ->and($idea->steps->pluck('description')->all())->toBe(['Buy a guitar', 'Find a teacher']);
    });

    it('falls back to defaults for null attributes', function (): void {
        $idea = app(CreateIdea::class)->handle(User::factory()->create(), [
            'title' => 'Minimal idea',
            'description' => null,
            'status' => null,
            'links' => null,
            'steps' => null,
        ]);

        $idea->refresh();

        expect($idea->status)->toBe(IdeaStatus::PENDING)
            ->and($idea->links->getArrayCopy())->toBe([])
            ->and($idea->steps()->count())->toBe(0);
    });

    it('trims steps and skips blank ones', function (): void {
        $idea = app(CreateIdea::class)->handle(User::factory()->create(), [
            'title' => 'Learn guitar',
            'steps' => ['  Practice chords  ', '   ', ''],
        ]);

        expect($idea->steps()->pluck('description')->all())->toBe(['Practice chords']);
    });

    it('stores the image on the public disk', function (): void {
        Storage::fake('public');

        $idea = app(CreateIdea::class)->handle(
            User::factory()->create(),
            ['title' => 'Build a garden'],
            UploadedFile::fake()->image('garden.jpg'),
        );

        expect($idea->image_path)->toStartWith('ideas/');
        Storage::disk('public')->assertExists($idea->image_path);
    });

    it('rolls back the idea when a step fails to save', function (): void {
        Step::creating(fn (): never => throw new RuntimeException('Step failed.'));

        expect(fn (): Idea => app(CreateIdea::class)->handle(User::factory()->create(), [
            'title' => 'Learn guitar',
            'steps' => ['Buy a guitar'],
        ]))->toThrow(RuntimeException::class, 'Step failed.');

        expect(Idea::query()->count())->toBe(0);
    });
})->group('feature', 'actions');
