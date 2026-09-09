<?php

declare(strict_types=1);

use App\Enums\IdeaStatus;

covers(IdeaStatus::class);

describe('IdeaStatus enum', function (): void {
    it('exposes exactly nine statuses', function (): void {
        expect(IdeaStatus::cases())->toHaveCount(9);
    });

    it('maps each case to its backing value and label', function (IdeaStatus $status, string $value, string $label): void {
        expect($status->value)->toBe($value)
            ->and($status->label())->toBe($label)
            ->and(IdeaStatus::from($value))->toBe($status);
    })->with('idea statuses');

    it('rejects unknown backing values', function (): void {
        expect(IdeaStatus::tryFrom('unknown'))->toBeNull();
    });

    it('has a unique label per case', function (): void {
        $labels = array_map(fn (IdeaStatus $status): string => $status->label(), IdeaStatus::cases());

        expect(array_unique($labels))->toHaveCount(count($labels));
    });
})->group('unit', 'models');
