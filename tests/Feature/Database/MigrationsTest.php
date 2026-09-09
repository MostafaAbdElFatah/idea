<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

describe('migrations', function (): void {
    it('can be rolled back and re-run', function (): void {
        $this->artisan('migrate:rollback')->assertExitCode(0);
        expect(Schema::hasTable('ideas'))->toBeFalse()
            ->and(Schema::hasTable('steps'))->toBeFalse();

        $this->artisan('migrate')->assertExitCode(0);
        expect(Schema::hasTable('ideas'))->toBeTrue()
            ->and(Schema::hasTable('steps'))->toBeTrue();
    });

    it('has no pending migrations after refresh', function (): void {
        $this->artisan('migrate:status')->assertExitCode(0)->doesntExpectOutputToContain('Pending');
    });
})->group('feature', 'database');
