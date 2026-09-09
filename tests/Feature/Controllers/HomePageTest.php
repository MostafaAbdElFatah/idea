<?php

declare(strict_types=1);

use function Pest\Laravel\get;

describe('home page', function (): void {
    it('renders for guests', function (): void {
        get('/')
            ->assertOk()
            ->assertViewIs('welcome')
            ->assertSee(config('app.name'));
    });

    it('renders for authenticated users', function (): void {
        loginAs();

        get('/')->assertOk()->assertViewIs('welcome');
    });

    it('does not advertise auth routes that do not exist', function (): void {
        get('/')->assertDontSee('href="/login"', false)->assertDontSee('href="/register"', false);
    });

    it('returns 404 for unknown paths', function (): void {
        get('/does-not-exist')->assertNotFound();
    });

    it('does not expose the unrouted idea endpoints', function (string $path): void {
        get($path)->assertNotFound();
    })->with(['/ideas', '/ideas/1', '/steps', '/steps/1']);
})->group('feature', 'controllers');
