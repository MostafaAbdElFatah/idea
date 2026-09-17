<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

describe('authentication pages', function (): void {
    it('render the login page without console errors', function (): void {
        visit(route('login'))->assertNoSmoke()->assertSee('Email');
    });

    it('render the register page without console errors', function (): void {
        visit(route('register'))
            ->assertNoSmoke()
            ->assertSee('First name')
            ->assertSee('Last name');
    });

    it('render the register page on mobile', function (): void {
        visit(route('register'))->on()->mobile()->assertNoSmoke()->assertSee('First name');
    });
})->group('browser');

describe('authentication flow', function (): void {
    it('lets a visitor register from the form', function (): void {
        visit(route('register'))
            ->fill('first_name', 'Jane')
            ->fill('last_name', 'Doe')
            ->fill('email', 'jane@example.com')
            ->fill('password', 'password123')
            ->fill('password_confirmation', 'password123')
            ->click('create Account')
            ->assertPathIs('/ideas')
            ->assertNoJavascriptErrors();

        expect(User::where('email', 'jane@example.com')->exists())->toBeTrue();
    });

    it('shows an error for invalid login credentials', function (): void {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        visit(route('login'))
            ->fill('email', 'jane@example.com')
            ->fill('password', 'wrong-password')
            ->click('Sign In')
            ->assertPathIs('/login')
            ->assertSee('These credentials do not match our records.');
    });

    it('lets an existing user log in', function (): void {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        visit(route('login'))
            ->fill('email', 'jane@example.com')
            ->fill('password', 'password123')
            ->click('Sign In')
            ->assertPathIs('/ideas')
            ->assertNoJavascriptErrors();
    });
})->group('browser');

describe('profile', function (): void {
    it('opens the account menu and logs out from it', function (): void {
        loginAs(User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'jane@example.com']));

        visit(route('idea.index'))
            ->assertMissing('[role="menu"]')
            ->click('[aria-label="Account menu"]')
            ->assertVisible('[role="menu"]')
            ->assertSee('jane@example.com')
            ->assertSee('Settings')
            ->click('[role="menuitem"]:text-is("Log out")')
            ->assertVisible('[role="alertdialog"]:visible')
            ->assertSee('Log out?')
            ->click('[role="alertdialog"]:visible button[type="submit"]')
            ->assertPathIs('/login')
            ->assertNoJavascriptErrors();

        $this->assertGuest();
    });

    it('keeps the user logged in when logout is cancelled', function (): void {
        loginAs();

        visit(route('idea.index'))
            ->click('[aria-label="Account menu"]')
            ->click('[role="menuitem"]:text-is("Log out")')
            ->click('[role="alertdialog"]:visible button:text-is("Cancel")')
            ->assertMissing('[role="alertdialog"]:visible')
            ->assertPathIs('/ideas');

        $this->assertAuthenticated();
    });

    it('asks for confirmation before removing the profile photo', function (): void {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('me.jpg')->store('profile-images', 'public');
        $user = loginAs(User::factory()->create(['profile_image_path' => $path]));

        visit(route('profile.show'))
            ->click('Remove photo')
            ->assertSee('Remove profile photo?')
            ->click('[role="alertdialog"]:visible button:text-is("Cancel")')
            ->assertMissing('[role="alertdialog"]:visible');

        expect($user->fresh()->profile_image_path)->toBe($path);

        visit(route('profile.show'))
            ->click('Remove photo')
            ->click('[role="alertdialog"]:visible button[type="submit"]')
            ->assertSee('Profile photo removed.')
            ->assertNoJavascriptErrors();

        expect($user->fresh()->profile_image_path)->toBeNull();
    });

    it('goes to the profile settings from the account menu', function (): void {
        loginAs();

        visit(route('idea.index'))
            ->click('[aria-label="Account menu"]')
            ->click('[role="menuitem"]:text-is("Settings")')
            ->assertPathIs('/profile')
            ->assertSee('Personal information');
    });

    it('renders the profile page without console errors', function (): void {
        loginAs(User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']));

        visit(route('profile.show'))
            ->assertNoSmoke()
            ->assertSee('Jane Doe')
            ->assertSee('Personal information');
    });

    it('lets the user update their name from the profile page', function (): void {
        $user = loginAs(User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']));

        visit(route('profile.show'))
            ->fill('first_name', 'Janet')
            ->click('Save changes')
            ->assertSee('Profile updated successfully.')
            ->assertSee('Janet Doe')
            ->assertNoJavascriptErrors();

        expect($user->fresh()->first_name)->toBe('Janet');
    });

    it('changes the password from the dialog', function (): void {
        $user = loginAs(User::factory()->create(['password' => 'old-password']));

        visit(route('profile.show'))
            ->assertMissing('[aria-labelledby*="change-password"]')
            ->click('button[type="button"]:text-is("Change password")')
            ->fill('current_password', 'old-password')
            ->fill('password', 'new-password')
            ->fill('password_confirmation', 'new-password')
            ->click('button[type="submit"]:text-is("Change password")')
            ->assertSee('Password changed successfully.')
            ->assertNoJavascriptErrors();

        expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
    });

    it('shows the email as read-only and reopens the email dialog on errors', function (): void {
        loginAs(User::factory()->create(['email' => 'jane@example.com', 'password' => 'secret-password']));

        visit(route('profile.show'))
            ->assertAttribute('#current_email', 'disabled', '')
            ->click('button[type="button"]:text-is("Change email")')
            ->fill('email', 'janet@example.com')
            ->fill('email_password', 'wrong-password')
            ->click('button[type="submit"]:text-is("Change email")')
            ->assertSee('The password is incorrect.')
            ->assertNoJavascriptErrors();
    });

    it('deletes the account from the dialog', function (): void {
        $user = loginAs(User::factory()->create(['password' => 'secret-password']));

        visit(route('profile.show'))
            ->click('button[type="button"]:text-is("Delete account")')
            ->fill('delete_password', 'secret-password')
            ->click('Delete my account')
            ->assertPathIs('/login')
            ->assertSee('Your account has been deleted.')
            ->assertNoJavascriptErrors();

        expect($user->fresh())->toBeNull();
    });

    it('previews a picked avatar on the register page and can undo it', function (): void {
        $image = UploadedFile::fake()->image('me.jpg', 300, 300);

        visit(route('register'))
            ->assertMissing('img[alt="Profile photo preview"]')
            ->attach('#profile_image', $image->getPathname())
            ->assertVisible('img[alt="Profile photo preview"]')
            ->click('Undo')
            ->assertMissing('img[alt="Profile photo preview"]')
            ->assertNoJavascriptErrors();
    });
})->group('browser');
