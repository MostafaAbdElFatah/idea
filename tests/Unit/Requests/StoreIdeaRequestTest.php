<?php

declare(strict_types=1);

use App\Http\Requests\StoreideaRequest;

covers(StoreideaRequest::class);

describe('StoreideaRequest', function (): void {
    it('does not authorize anyone yet', function (): void {
        expect((new StoreideaRequest)->authorize())->toBeFalse();
    });

    it('declares no validation rules yet', function (): void {
        expect((new StoreideaRequest)->rules())->toBe([]);
    });
})->group('unit', 'requests');
