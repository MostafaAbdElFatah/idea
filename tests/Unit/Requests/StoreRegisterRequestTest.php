<?php

declare(strict_types=1);

use App\Http\Requests\StoreRegisterRequest;

covers(StoreRegisterRequest::class);

describe('StoreRegisterRequest', function (): void {
    it('declares rules for every registration field', function (): void {
        expect(array_keys((new StoreRegisterRequest)->rules()))
            ->toBe(['first_name', 'last_name', 'email', 'password']);
    });

    it('requires each field', function (string $field): void {
        expect((new StoreRegisterRequest)->rules()[$field])->toContain('required');
    })->with(['first_name', 'last_name', 'email', 'password']);

    it('enforces name length bounds', function (string $field): void {
        expect((new StoreRegisterRequest)->rules()[$field])
            ->toContain('min:3')
            ->toContain('max:255');
    })->with(['first_name', 'last_name']);

    it('validates and bounds the email', function (): void {
        expect((new StoreRegisterRequest)->rules()['email'])
            ->toContain('email')
            ->toContain('max:255');
    });

    it('requires a confirmed password', function (): void {
        expect((new StoreRegisterRequest)->rules()['password'])->toContain('confirmed');
    });
})->group('unit', 'requests');
