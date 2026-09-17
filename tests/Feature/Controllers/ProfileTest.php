<?php

declare(strict_types=1);

use App\Enums\IdeaStatus;
use App\Http\Controllers\ProfileBannerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileEmailController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\ProfilePasswordController;
use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\ProfileDialogRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\delete;
use function Pest\Laravel\from;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

covers(
    ProfileController::class,
    ProfileImageController::class,
    ProfileEmailController::class,
    ProfilePasswordController::class,
    ProfileBannerController::class,
    UpdateProfileRequest::class,
    UpdateEmailRequest::class,
    UpdatePasswordRequest::class,
    UpdateBannerRequest::class,
    DeleteAccountRequest::class,
    ProfileDialogRequest::class,
);

/**
 * @return array{first_name: string, last_name: string}
 */
function profilePayload(User $user, array $overrides = []): array
{
    return array_merge([
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
    ], $overrides);
}

describe('profile page', function (): void {
    it('shows the user details and idea stats', function (): void {
        $user = loginAs(User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'jane@example.com']));
        Idea::factory()->for($user)->create(['status' => IdeaStatus::COMPLETED]);
        $inProgress = Idea::factory()->for($user)->create(['status' => IdeaStatus::IN_PROGRESS]);
        Step::factory()->for($inProgress)->count(2)->create(['completed' => true]);
        Step::factory()->for($inProgress)->create(['completed' => false]);

        get(route('profile.show'))
            ->assertOk()
            ->assertViewIs('profile.show')
            ->assertViewHas('stats', ['ideas' => 2, 'completed' => 1, 'inProgress' => 1, 'stepsDone' => 2])
            ->assertSee('Jane Doe')
            ->assertSee('jane@example.com')
            ->assertSee('JD');
    });

    it('shows the account menu with the profile image or initials', function (): void {
        $user = loginAs(User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']));

        get(route('idea.index'))
            ->assertSee('aria-label="Account menu"', false)
            ->assertSee('JD')
            ->assertSee('href="'.route('profile.show').'#settings"', false)
            ->assertSee('Log out')
            ->assertSee('Log out?')
            ->assertSee('logoutDialogOpen', false)
            ->assertDontSee('//here add profile image');

        $user->update(['profile_image_path' => 'profile-images/me.jpg']);

        get(route('idea.index'))
            ->assertSee('src="'.$user->profileImageUrl.'"', false);
    });

    it('only counts the ideas and steps of the current user', function (): void {
        $other = Idea::factory()->create(['status' => IdeaStatus::COMPLETED]);
        Step::factory()->for($other)->create(['completed' => true]);
        loginAs();

        get(route('profile.show'))
            ->assertViewHas('stats', ['ideas' => 0, 'completed' => 0, 'inProgress' => 0, 'stepsDone' => 0]);
    });

    it('requires authentication', function (): void {
        get(route('profile.show'))->assertRedirect(route('login'));
        patch(route('profile.update'))->assertRedirect(route('login'));
        delete(route('profile.image.destroy'))->assertRedirect(route('login'));
        delete(route('profile.destroy'))->assertRedirect(route('login'));
        put(route('profile.password.update'))->assertRedirect(route('login'));
        put(route('profile.email.update'))->assertRedirect(route('login'));
        post(route('profile.banner.update'))->assertRedirect(route('login'));
        delete(route('profile.banner.destroy'))->assertRedirect(route('login'));
    });
})->group('feature', 'controllers');

describe('updating the profile', function (): void {
    it('updates the name', function (): void {
        $user = loginAs(User::factory()->create(['email' => 'jane@example.com']));

        patch(route('profile.update'), profilePayload($user, [
            'first_name' => '  Janet  ',
            'last_name' => 'Smith',
        ]))
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('success', 'Profile updated successfully.');

        $user->refresh();

        expect($user->first_name)->toBe('Janet')
            ->and($user->last_name)->toBe('Smith')
            ->and($user->email)->toBe('jane@example.com');
    });

    it('ignores email and password fields sent to the profile form', function (): void {
        $user = loginAs(User::factory()->create(['email' => 'jane@example.com', 'password' => 'old-password']));

        patch(route('profile.update'), profilePayload($user, [
            'email' => 'hacker@example.com',
            'password' => 'new-password',
        ]))->assertSessionHasNoErrors();

        $user->refresh();

        expect($user->email)->toBe('jane@example.com')
            ->and(Hash::check('old-password', $user->password))->toBeTrue();
    });

    it('replaces the profile image and deletes the old file', function (): void {
        Storage::fake('public');
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('profile-images', 'public');
        $user = loginAs(User::factory()->create(['profile_image_path' => $oldPath]));

        patch(route('profile.update'), profilePayload($user, [
            'profile_image' => UploadedFile::fake()->image('new.jpg'),
        ]))->assertSessionHasNoErrors();

        $newPath = $user->fresh()->profile_image_path;

        expect($newPath)->toStartWith('profile-images/')->not->toBe($oldPath);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    });

    it('rejects a profile image that is not an image or too large', function (UploadedFile $file): void {
        Storage::fake('public');
        $user = loginAs();

        patch(route('profile.update'), profilePayload($user, ['profile_image' => $file]))
            ->assertSessionHasErrors('profile_image');
    })->with([
        'not an image' => fn (): UploadedFile => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
        'too large' => fn (): UploadedFile => UploadedFile::fake()->image('big.jpg')->size(3000),
    ]);
})->group('feature', 'controllers');

describe('changing the email', function (): void {
    it('changes the email when the current password is correct', function (): void {
        $user = loginAs(User::factory()->create(['email' => 'jane@example.com', 'password' => 'secret-password']));

        put(route('profile.email.update'), [
            'email' => '  janet@example.com ',
            'email_password' => 'secret-password',
        ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('success', 'Email changed successfully.');

        expect($user->fresh()->email)->toBe('janet@example.com');
    });

    it('rejects invalid input and reopens the email dialog', function (array $payload, string $field): void {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = loginAs(User::factory()->create(['email' => 'jane@example.com', 'password' => 'secret-password']));

        from(route('profile.show'))
            ->put(route('profile.email.update'), $payload)
            ->assertRedirect(route('profile.show'))
            ->assertSessionHasErrorsIn('updateEmail', $field)
            ->assertSessionHas('open_modal', 'change-email');

        expect($user->fresh()->email)->toBe('jane@example.com');
    })->with([
        'taken email' => [['email' => 'taken@example.com', 'email_password' => 'secret-password'], 'email'],
        'invalid email' => [['email' => 'not-an-email', 'email_password' => 'secret-password'], 'email'],
        'wrong password' => [['email' => 'janet@example.com', 'email_password' => 'wrong'], 'email_password'],
        'missing password' => [['email' => 'janet@example.com'], 'email_password'],
    ]);
})->group('feature', 'controllers');

describe('changing the password', function (): void {
    it('changes the password when the current password is correct', function (): void {
        $user = loginAs(User::factory()->create(['password' => 'old-password']));

        put(route('profile.password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('success', 'Password changed successfully.');

        expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
    });

    it('rejects invalid input and reopens the password dialog', function (array $payload, string $field): void {
        $user = loginAs(User::factory()->create(['password' => 'old-password']));

        from(route('profile.show'))
            ->put(route('profile.password.update'), $payload)
            ->assertRedirect(route('profile.show'))
            ->assertSessionHasErrorsIn('updatePassword', $field)
            ->assertSessionHas('open_modal', 'change-password')
            ->assertSessionMissing('_old_input.password');

        expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
    })->with([
        'wrong current password' => [['current_password' => 'wrong', 'password' => 'new-password', 'password_confirmation' => 'new-password'], 'current_password'],
        'unconfirmed password' => [['current_password' => 'old-password', 'password' => 'new-password', 'password_confirmation' => 'different'], 'password'],
        'missing new password' => [['current_password' => 'old-password'], 'password'],
    ]);
})->group('feature', 'controllers');

describe('profile banner', function (): void {
    it('uploads a banner and replaces the old file', function (): void {
        Storage::fake('public');
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('profile-banners', 'public');
        $user = loginAs(User::factory()->create(['banner_image_path' => $oldPath]));

        post(route('profile.banner.update'), ['banner_image' => UploadedFile::fake()->image('cover.jpg', 1200, 400)])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('success', 'Cover image updated.');

        $newPath = $user->fresh()->banner_image_path;

        expect($newPath)->toStartWith('profile-banners/');
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    });

    it('rejects a missing or invalid banner', function (array $payload): void {
        Storage::fake('public');
        loginAs();

        post(route('profile.banner.update'), $payload)->assertSessionHasErrorsIn('updateBanner', 'banner_image');
    })->with([
        'missing' => fn (): array => [],
        'not an image' => fn (): array => ['banner_image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')],
    ]);

    it('removes the banner', function (): void {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('cover.jpg')->store('profile-banners', 'public');
        $user = loginAs(User::factory()->create(['banner_image_path' => $path]));

        delete(route('profile.banner.destroy'))
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('success', 'Cover image removed.');

        Storage::disk('public')->assertMissing($path);
        expect($user->fresh()->banner_image_path)->toBeNull();
    });

    it('shows the banner image or the placeholder', function (): void {
        $user = loginAs();

        get(route('profile.show'))->assertSee('Add cover')->assertDontSee('alt="Profile cover"', false);

        $user->update(['banner_image_path' => 'profile-banners/cover.jpg']);

        get(route('profile.show'))
            ->assertSee('Change cover')
            ->assertSee('src="'.$user->bannerImageUrl.'"', false);
    });
})->group('feature', 'controllers');

describe('deleting the account', function (): void {
    it('deletes the user, their ideas, and their images, then logs out', function (): void {
        Storage::fake('public');
        $profile = UploadedFile::fake()->image('me.jpg')->store('profile-images', 'public');
        $banner = UploadedFile::fake()->image('cover.jpg')->store('profile-banners', 'public');
        $ideaImage = UploadedFile::fake()->image('idea.jpg')->store('ideas', 'public');
        $user = loginAs(User::factory()->create([
            'password' => 'secret-password',
            'profile_image_path' => $profile,
            'banner_image_path' => $banner,
        ]));
        $idea = Idea::factory()->for($user)->create(['image_path' => $ideaImage]);
        Step::factory()->for($idea)->create();
        $otherIdea = Idea::factory()->create();

        delete(route('profile.destroy'), ['delete_password' => 'secret-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Your account has been deleted.');

        $this->assertGuest();
        expect($user->fresh())->toBeNull()
            ->and(Idea::query()->whereKey($idea->id)->exists())->toBeFalse()
            ->and(Step::query()->count())->toBe(0)
            ->and($otherIdea->fresh())->not->toBeNull();
        Storage::disk('public')->assertMissing([$profile, $banner, $ideaImage]);
    });

    it('keeps the account when the password is wrong and reopens the dialog', function (?string $password): void {
        $user = loginAs(User::factory()->create(['password' => 'secret-password']));

        from(route('profile.show'))
            ->delete(route('profile.destroy'), ['delete_password' => $password])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHasErrorsIn('deleteAccount', 'delete_password')
            ->assertSessionHas('open_modal', 'delete-account');

        $this->assertAuthenticatedAs($user);
        expect($user->fresh())->not->toBeNull();
    })->with(['wrong' => 'wrong-password', 'missing' => null]);
})->group('feature', 'controllers');

describe('removing the profile image', function (): void {
    it('deletes the file and clears the path', function (): void {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('me.jpg')->store('profile-images', 'public');
        $user = loginAs(User::factory()->create(['profile_image_path' => $path]));

        delete(route('profile.image.destroy'))
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('success', 'Profile photo removed.');

        Storage::disk('public')->assertMissing($path);
        expect($user->fresh()->profile_image_path)->toBeNull();
    });

    it('succeeds when there is no profile image', function (): void {
        loginAs();

        delete(route('profile.image.destroy'))->assertRedirect(route('profile.show'));
    });
})->group('feature', 'controllers');
