<?php

declare(strict_types=1);

use App\Models\Idea;
use Illuminate\Support\Facades\Storage;

describe('idea image component', function (): void {
    it('renders the idea image with the given classes', function (): void {
        Storage::fake('public');
        $idea = new Idea(['title' => 'Build a garden', 'image_path' => 'ideas/garden.jpg']);

        $this->blade('<x-idea.image :idea="$idea" class="mt-10" />', ['idea' => $idea])
            ->assertSee('src="'.Storage::disk('public')->url('ideas/garden.jpg').'"', false)
            ->assertSee('alt="Build a garden"', false)
            ->assertSee('class="overflow-hidden mt-10"', false);
    });

    it('renders a label only when one is given', function (): void {
        Storage::fake('public');
        $idea = new Idea(['title' => 'Build a garden', 'image_path' => 'ideas/garden.jpg']);

        $this->blade('<x-idea.image :idea="$idea" label="Cover image" />', ['idea' => $idea])
            ->assertSee('<label class="label">Cover image</label>', false);

        $this->blade('<x-idea.image :idea="$idea" />', ['idea' => $idea])
            ->assertDontSee('<label', false);
    });

    it('renders nothing when the idea has no image', function (): void {
        $this->blade('<x-idea.image :idea="$idea" />', ['idea' => new Idea(['title' => 'No image'])])
            ->assertDontSee('<img', false);
    });
})->group('feature', 'views');
