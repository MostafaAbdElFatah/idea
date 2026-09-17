<?php

declare(strict_types=1);

use App\Actions\UpdateIdea;
use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\Step;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

covers(UpdateIdea::class);

describe('UpdateIdea action', function (): void {
    it('updates the idea fields', function (): void {
        $idea = Idea::factory()->create([
            'title' => 'Old title',
            'description' => 'Old description',
            'status' => IdeaStatus::PENDING,
            'links' => ['https://old.example.com'],
        ]);

        app(UpdateIdea::class)->handle($idea, [
            'title' => 'New title',
            'description' => 'New description',
            'status' => IdeaStatus::ACTIVE->value,
            'links' => ['https://new.example.com'],
        ]);

        $idea->refresh();

        expect($idea->title)->toBe('New title')
            ->and($idea->description)->toBe('New description')
            ->and($idea->status)->toBe(IdeaStatus::ACTIVE)
            ->and($idea->links->getArrayCopy())->toBe(['https://new.example.com']);
    });

    it('keeps the status, clears the description, and empties links when they are not given', function (): void {
        $idea = Idea::factory()->create([
            'description' => 'Old description',
            'status' => IdeaStatus::ACTIVE,
            'links' => ['https://old.example.com'],
        ]);

        app(UpdateIdea::class)->handle($idea, [
            'title' => 'New title',
            'description' => null,
            'status' => null,
            'links' => null,
        ]);

        $idea->refresh();

        expect($idea->status)->toBe(IdeaStatus::ACTIVE)
            ->and($idea->description)->toBeNull()
            ->and($idea->links->getArrayCopy())->toBe([]);
    });

    it('syncs steps and keeps the completed state of kept steps', function (): void {
        $idea = Idea::factory()->create();
        $kept = Step::factory()->for($idea)->create(['description' => 'Buy a guitar', 'completed' => true]);
        $removed = Step::factory()->for($idea)->create(['description' => 'Old step']);

        app(UpdateIdea::class)->handle($idea, [
            'title' => $idea->title,
            'steps' => ['Buy a guitar', '  Find a teacher  ', '', 'Find a teacher'],
        ]);

        $steps = $idea->steps()->orderBy('id')->get();

        expect($steps->pluck('description')->all())->toBe(['Buy a guitar', 'Find a teacher'])
            ->and($steps->first()->is($kept))->toBeTrue()
            ->and((bool) $steps->first()->completed)->toBeTrue()
            ->and($removed->fresh())->toBeNull();
    });

    it('removes all steps when none are given', function (): void {
        $idea = Idea::factory()->create();
        Step::factory()->for($idea)->count(2)->create();

        app(UpdateIdea::class)->handle($idea, ['title' => $idea->title]);

        expect($idea->steps()->count())->toBe(0);
    });

    it('replaces the image and deletes the old file', function (): void {
        Storage::fake('public');
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('ideas', 'public');
        $idea = Idea::factory()->create(['image_path' => $oldPath]);

        app(UpdateIdea::class)->handle($idea, ['title' => $idea->title], UploadedFile::fake()->image('new.jpg'));

        expect($idea->refresh()->image_path)->toStartWith('ideas/')->not->toBe($oldPath);
        Storage::disk('public')->assertExists($idea->image_path);
        Storage::disk('public')->assertMissing($oldPath);
    });

    it('keeps the current image when no new one is given', function (): void {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('old.jpg')->store('ideas', 'public');
        $idea = Idea::factory()->create(['image_path' => $path]);

        app(UpdateIdea::class)->handle($idea, ['title' => 'New title']);

        expect($idea->refresh()->image_path)->toBe($path);
        Storage::disk('public')->assertExists($path);
    });

    it('rolls back the update when a step fails to save', function (): void {
        $idea = Idea::factory()->create(['title' => 'Old title']);
        Step::creating(fn (): never => throw new RuntimeException('Step failed.'));

        expect(fn (): Idea => app(UpdateIdea::class)->handle($idea, [
            'title' => 'New title',
            'steps' => ['Buy a guitar'],
        ]))->toThrow(RuntimeException::class, 'Step failed.');

        expect($idea->fresh()->title)->toBe('Old title');
    });
})->group('feature', 'actions');
