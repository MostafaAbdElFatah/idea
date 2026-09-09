<?php

declare(strict_types=1);

describe('home page', function (): void {
    it('renders for guests', function (): void {
        $this->get('/')
            ->assertOk()
            ->assertViewIs('welcome')
            ->assertSee(config('app.name'));
    });

    it('renders for authenticated users', function (): void {
        loginAs();

        $this->get('/')->assertOk()->assertViewIs('welcome');
    });

    it('does not advertise auth routes that do not exist', function (): void {
        $this->get('/')->assertDontSee('href="/login"', false)->assertDontSee('href="/register"', false);
    });

    it('returns 404 for unknown paths', function (): void {
        $this->get('/does-not-exist')->assertNotFound();
    });

    it('does not expose the unrouted idea endpoints', function (string $path): void {
        $this->get($path)->assertNotFound();
    })->with(['/ideas', '/ideas/1', '/steps', '/steps/1']);
})->group('feature', 'controllers');
