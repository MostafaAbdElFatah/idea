<?php

declare(strict_types=1);

use App\Http\Requests\StoreIdeaRequest;
use Illuminate\Support\Facades\Validator;

covers(StoreIdeaRequest::class);

describe('StoreIdeaRequest', function (): void {
    it('accepts a valid payload', function (): void {
        $validator = Validator::make([
            'title' => 'Learn guitar',
            'links' => ['https://example.com'],
            'steps' => ['Buy a guitar'],
        ], (new StoreIdeaRequest)->rules());

        expect($validator->passes())->toBeTrue();
    });

    it('rejects invalid fields', function (array $payload, string $field): void {
        $validator = Validator::make($payload, (new StoreIdeaRequest)->rules());

        expect($validator->errors()->has($field))->toBeTrue();
    })->with([
        'missing title' => [[], 'title'],
        'short title' => [['title' => 'ab'], 'title'],
        'unknown status' => [['title' => 'Valid', 'status' => 'nope'], 'status'],
        'invalid link' => [['title' => 'Valid', 'links' => ['nope']], 'links.0'],
        'long step' => [['title' => 'Valid', 'steps' => [str_repeat('a', 256)]], 'steps.0'],
    ]);
})->group('unit', 'requests');
