<?php

declare(strict_types=1);

use App\Http\Requests\UpdateideaRequest;

covers(UpdateideaRequest::class);

describe('UpdateideaRequest', function (): void {
    it('does not authorize anyone yet', function (): void {
        expect((new UpdateideaRequest)->authorize())->toBeFalse();
    });

    it('declares no validation rules yet', function (): void {
        expect((new UpdateideaRequest)->rules())->toBe([]);
    });
})->group('unit', 'requests');
