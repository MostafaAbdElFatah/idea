<?php

declare(strict_types=1);

describe('public pages', function (): void {
    it('render without console errors on desktop', function (): void {
        visit('/')->assertNoSmoke()->assertSee("Let's get started");
    });

    it('render without console errors on mobile', function (): void {
        visit('/')->on()->mobile()->assertNoSmoke()->assertSee("Let's get started");
    });

    it('render in dark mode', function (): void {
        visit('/')->inDarkMode()->assertNoJavascriptErrors()->assertSee("Let's get started");
    });

    it('show a 404 page for unknown paths without javascript errors', function (): void {
        visit('/missing')->assertNoJavascriptErrors()->assertSee('404');
    });
})->group('browser');
