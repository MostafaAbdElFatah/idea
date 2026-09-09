<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Schedule;

describe('console', function (): void {
    it('prints an inspiring quote', function (): void {
        $this->artisan('inspire')->assertExitCode(0)->run();
    });

    it('registers no scheduled tasks yet', function (): void {
        expect(app(Schedule::class)->events())->toBeEmpty();
    });
})->group('feature', 'console');
