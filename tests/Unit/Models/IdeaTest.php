<?php

declare(strict_types=1);

use App\Enums\IdeaStatus;
use App\Models\Idea;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

covers(Idea::class);

describe('Idea model configuration', function (): void {
    it('defaults a new idea to the pending status', function (): void {
        $idea = new Idea;

        expect($idea->status)->toBe(IdeaStatus::PENDING)
            ->and($idea->getAttributes()['status'])->toBe('pending');
    });

    it('casts status to the IdeaStatus enum', function (): void {
        expect((new Idea)->getCasts())->toHaveKey('status', IdeaStatus::class);
    });

    it('casts links to an array object', function (): void {
        expect((new Idea)->getCasts())->toHaveKey('links', AsArrayObject::class);
    });

    it('accepts an enum status on assignment', function (): void {
        $idea = new Idea(['status' => IdeaStatus::ARCHIVED]);

        expect($idea->status)->toBe(IdeaStatus::ARCHIVED)
            ->and($idea->getAttributes()['status'])->toBe('archived');
    });

    it('accepts a raw string status and exposes it as an enum', function (IdeaStatus $status, string $value): void {
        $idea = new Idea(['status' => $value]);

        expect($idea->status)->toBe($status);
    })->with('idea statuses');

    it('exposes links as an ArrayObject', function (): void {
        $idea = new Idea(['links' => ['https://example.com']]);

        expect($idea->links)->toBeInstanceOf(ArrayObject::class)
            ->and($idea->links->getArrayCopy())->toBe(['https://example.com'])
            ->and($idea->getAttributes()['links'])->toBe('["https:\/\/example.com"]');
    });

    it('uses the ideas table and an auto-incrementing id', function (): void {
        $idea = new Idea;

        expect($idea->getTable())->toBe('ideas')
            ->and($idea->getKeyName())->toBe('id')
            ->and($idea->getIncrementing())->toBeTrue();
    });
})->group('unit', 'models');
