<?php

declare(strict_types=1);

use App\Http\Controllers\Ideas\IdeaImageController;
use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\delete;
use function Pest\Laravel\from;
use function Pest\Laravel\get;

covers(IdeaImageController::class);

describe('removing an idea image', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
    });

    it('deletes the image file and clears the image path for the owner', function (): void {
        $user = loginAs();
        $path = UploadedFile::fake()->image('garden.jpg')->store('ideas', 'public');
        $idea = Idea::factory()->for($user)->create(['image_path' => $path]);

        from(route('idea.show', $idea))
            ->delete(route('idea.image.destroy', $idea))
            ->assertRedirect(route('idea.show', $idea))
            ->assertSessionHas('success', 'Image removed successfully.')
            ->assertSessionMissing('open_modal');

        Storage::disk('public')->assertMissing($path);
        expect($idea->fresh()->image_path)->toBeNull();
    });

    it('succeeds when the idea has no image', function (): void {
        $idea = Idea::factory()->for(loginAs())->create(['image_path' => null]);

        delete(route('idea.image.destroy', $idea))->assertRedirect();

        expect($idea->fresh()->image_path)->toBeNull();
    });

    it('hides the idea image from other users', function (): void {
        $path = UploadedFile::fake()->image('garden.jpg')->store('ideas', 'public');
        $idea = Idea::factory()->create(['image_path' => $path]);
        loginAs();

        delete(route('idea.image.destroy', $idea))->assertNotFound();

        Storage::disk('public')->assertExists($path);
        expect($idea->fresh()->image_path)->toBe($path);
    });

    it('requires authentication', function (): void {
        $idea = Idea::factory()->create();

        delete(route('idea.image.destroy', $idea))->assertRedirect(route('login'));
    });

    it('reopens the edit dialog with the success message after removing the image', function (): void {
        $idea = Idea::factory()->for(loginAs())->create(['image_path' => 'ideas/garden.jpg']);

        from(route('idea.show', $idea))
            ->followingRedirects()
            ->delete(route('idea.image.destroy', $idea), ['reopen_dialog' => '1'])
            ->assertOk()
            ->assertSee('Image removed successfully.')
            ->assertSee('show: true', false);
    });

    it('shows remove controls for the page and the edit dialog only when the idea has an image', function (): void {
        $user = loginAs();
        $withImage = Idea::factory()->for($user)->create(['image_path' => 'ideas/garden.jpg']);
        $withoutImage = Idea::factory()->for($user)->create(['image_path' => null]);

        get(route('idea.show', $withImage))
            ->assertOk()
            ->assertSee('id="remove-idea-image-inline"', false)
            ->assertSee('form="remove-idea-image-inline"', false)
            ->assertSee('id="remove-idea-image"', false)
            ->assertSee('form="remove-idea-image"', false)
            ->assertSee('name="reopen_dialog"', false)
            ->assertSee('Remove image?')
            ->assertSee('Replace image')
            ->assertDontSee('Click to add an image');

        get(route('idea.show', $withoutImage))
            ->assertOk()
            ->assertDontSee('remove-idea-image', false)
            ->assertDontSee('Remove image?')
            ->assertSee('Click to add an image')
            ->assertDontSee('Replace image');
    });
})->group('feature', 'controllers');
