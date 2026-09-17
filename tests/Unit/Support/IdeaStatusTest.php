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

    it('maps each case to its color', function (IdeaStatus $status, string $color): void {
        expect($status->color())->toBe($color);
    })->with([
        'pending' => [IdeaStatus::PENDING, 'yellow'],
        'in progress' => [IdeaStatus::IN_PROGRESS, 'blue'],
        'active' => [IdeaStatus::ACTIVE, 'green'],
        'completed' => [IdeaStatus::COMPLETED, 'teal'],
        'incompleted' => [IdeaStatus::INCOMPLETED, 'orange'],
        'draft' => [IdeaStatus::DRAFT, 'gray'],
        'paused' => [IdeaStatus::PAUSED, 'amber'],
        'cancelled' => [IdeaStatus::CANCELLED, 'red'],
        'archived' => [IdeaStatus::ARCHIVED, 'purple'],
    ]);

    it('checks whether a value is a known status, ignoring case and whitespace', function (string $value, bool $expected): void {
        expect(IdeaStatus::has($value))->toBe($expected);
    })->with([
        'exact value' => ['pending', true],
        'mixed case with whitespace' => ['  In_Progress ', true],
        'unknown value' => ['unknown', false],
        'empty string' => ['', false],
    ]);
})->group('unit', 'models');
