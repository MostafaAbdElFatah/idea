<?php

declare(strict_types=1);

use App\Models\Step;

covers(Step::class);

describe('Step model configuration', function (): void {
    it('uses the steps table', function (): void {
        expect((new Step)->getTable())->toBe('steps');
    });

    it('holds description and completion state', function (): void {
        $step = new Step(['description' => 'Write tests', 'completed' => true]);

        expect($step->description)->toBe('Write tests')
            ->and($step->completed)->toBeTrue();
    });

    it('declares the idea foreign key', function (): void {
        expect((new Step)->idea()->getForeignKeyName())->toBe('idea_id');
    });
})->group('unit', 'models');
