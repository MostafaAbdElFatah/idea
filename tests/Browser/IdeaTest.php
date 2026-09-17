<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\Step;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('ideas pages', function (): void {
    it('render the ideas page without console errors', function (): void {
        loginAs();

        visit(route('idea.index'))->assertNoSmoke()->assertSee("What's the idea?");
    });

    it('render the idea page with its steps', function (): void {
        $user = loginAs();
        $idea = Idea::factory()->for($user)->create(['title' => 'Learn guitar']);
        Step::factory()->for($idea)->incomplete()->create(['description' => 'Buy a guitar']);

        visit(route('idea.show', $idea))
            ->assertNoSmoke()
            ->assertSee('Learn guitar')
            ->assertSee('Buy a guitar');
    });
})->group('browser');

describe('idea flow', function (): void {
    it('lets a user create an idea with steps and links', function (): void {
        $user = loginAs();

        visit(route('idea.index'))
            ->click("What's the idea?")
            ->fill('title', 'Learn guitar')
            ->fill('description', 'Play a song by summer')
            ->fill('#step', 'Buy a guitar')
            ->click('[aria-label="Add step"]')
            ->fill('#step', 'Find a teacher')
            ->click('[aria-label="Add step"]')
            ->fill('#url', 'https://example.com')
            ->click('[aria-label="Add link"]')
            ->click('Create')
            ->assertSee('Idea created successfully.')
            ->assertSee('Learn guitar')
            ->assertSee('Buy a guitar')
            ->assertSee('Find a teacher')
            ->assertSee('https://example.com')
            ->assertNoJavascriptErrors();

        $idea = Idea::query()->with('steps')->sole();

        expect($idea->user_id)->toBe($user->id)
            ->and($idea->steps->pluck('description')->all())->toBe(['Buy a guitar', 'Find a teacher'])
            ->and($idea->links->getArrayCopy())->toBe(['https://example.com']);
    });

    it('closes the create dialog when cancel is clicked', function (): void {
        loginAs();

        visit(route('idea.index'))
            ->click("What's the idea?")
            ->assertVisible('[role="dialog"]')
            ->click('[role="dialog"] button:text-is("Cancel")')
            ->assertMissing('[role="dialog"]')
            ->assertNoJavascriptErrors();

        expect(Idea::query()->count())->toBe(0);
    });

    it('keeps added steps and links after a validation error', function (): void {
        loginAs();

        visit(route('idea.index'))
            ->click("What's the idea?")
            ->fill('title', 'ab')
            ->fill('#step', 'Buy a guitar')
            ->click('[aria-label="Add step"]')
            ->fill('#url', 'https://example.com')
            ->click('[aria-label="Add link"]')
            ->click('Create')
            ->assertPathIs('/ideas')
            ->assertSee('Buy a guitar')
            ->assertSee('https://example.com')
            ->assertNoJavascriptErrors();

        expect(Idea::query()->count())->toBe(0);
    });

    it('previews a selected image and returns to the placeholder when cleared', function (): void {
        loginAs();
        $image = UploadedFile::fake()->image('garden.jpg', 800, 400);

        visit(route('idea.index'))
            ->click("What's the idea?")
            ->assertSee('Click to add an image')
            ->attach('#image', $image->getPathname())
            ->assertVisible('img[alt="Selected image preview"]')
            ->assertSee('New image')
            ->assertDontSee('Click to add an image')
            ->click('[aria-label="Clear selected image"]')
            ->assertMissing('img[alt="Selected image preview"]')
            ->assertSee('Click to add an image')
            ->assertNoJavascriptErrors();
    });

    it('lets the owner edit an idea from the edit dialog', function (): void {
        $user = loginAs();
        $idea = Idea::factory()->for($user)->create(['title' => 'Learn guitar', 'links' => []]);
        Step::factory()->for($idea)->create(['description' => 'Buy a guitar']);

        visit(route('idea.show', $idea))
            ->click('Edit Idea')
            ->fill('title', 'Learn piano')
            ->fill('#step', 'Find a teacher')
            ->click('[aria-label="Add step"]')
            ->click('button[type="submit"]:text-is("Update")')
            ->assertSee('Idea updated successfully.')
            ->assertSee('Learn piano')
            ->assertSee('Find a teacher')
            ->assertNoJavascriptErrors();

        expect($idea->fresh()->title)->toBe('Learn piano')
            ->and($idea->steps()->pluck('description')->all())->toBe(['Buy a guitar', 'Find a teacher']);
    });

    it('lets the owner remove the idea image after confirming', function (): void {
        Storage::fake('public');
        $user = loginAs();
        $path = UploadedFile::fake()->image('garden.jpg', 1200, 600)->store('ideas', 'public');
        $idea = Idea::factory()->for($user)->create(['image_path' => $path]);

        $page = visit(route('idea.show', $idea))
            ->hover('img[alt="'.$idea->title.'"] >> nth=0')
            ->click('[aria-label="Remove image"] >> nth=0')
            ->assertSee('Remove image?')
            ->click('[aria-label="Confirm image removal"] >> nth=0 >> text=Keep')
            ->assertDontSee('Remove image?');

        expect($idea->fresh()->image_path)->toBe($path);

        $page->hover('img[alt="'.$idea->title.'"] >> nth=0')
            ->click('[aria-label="Remove image"] >> nth=0')
            ->click('[aria-label="Confirm image removal"] >> nth=0 >> button[type="submit"]')
            ->assertSee('Image removed successfully.')
            ->assertNoJavascriptErrors();

        Storage::disk('public')->assertMissing($path);
        expect($idea->fresh()->image_path)->toBeNull();
    });

    it('lets a user complete and reopen a step', function (): void {
        $user = loginAs();
        $idea = Idea::factory()->for($user)->create();
        $step = Step::factory()->for($idea)->incomplete()->create(['description' => 'Buy a guitar']);

        $page = visit(route('idea.show', $idea))
            ->click('[role="checkbox"]')
            ->assertSee('Step updated successfully.')
            ->assertNoJavascriptErrors();

        expect((bool) $step->fresh()->completed)->toBeTrue();

        $page->click('[role="checkbox"]')->assertSee('Step updated successfully.');

        expect((bool) $step->fresh()->completed)->toBeFalse();
    });
})->group('browser');
