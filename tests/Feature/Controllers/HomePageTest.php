<?php

declare(strict_types=1);

use function Pest\Laravel\get;

describe('home page', function (): void {
    it('redirects guests to the login page', function (): void {
        get('/')->assertRedirect(route('login'));
    });

    it('redirects authenticated users to their ideas', function (): void {
        loginAs();

        get('/')->assertRedirect('/ideas');
    });

    it('links guests between the login and register pages', function (): void {
        get(route('login'))->assertOk()->assertSeeHtml('href="/register"');
    });

    it('returns 404 for unknown paths', function (): void {
        get('/does-not-exist')->assertNotFound();
    });

    it('requires authentication for idea endpoints', function (string $path): void {
        get($path)->assertRedirect(route('login'));
    })->with(['/ideas', '/ideas/1']);

    it('does not expose a step index', function (): void {
        loginAs();

        get('/steps')->assertNotFound();
    });

    it('only accepts PATCH requests for a step', function (): void {
        loginAs();

        get('/steps/1')->assertMethodNotAllowed();
    });
})->group('feature', 'controllers');
