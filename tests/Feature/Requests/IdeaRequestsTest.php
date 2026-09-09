<?php

declare(strict_types=1);

use App\Http\Requests\StoreideaRequest;
use App\Http\Requests\UpdateideaRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;

covers(StoreideaRequest::class, UpdateideaRequest::class);

/**
 * The idea endpoints are not routed yet, so each request is mounted on a
 * throwaway route to prove the framework honours its authorize() answer.
 */
describe('idea form requests over HTTP', function (): void {
    it('forbids submissions because authorize() is false', function (string $requestClass): void {
        Route::post('/_test/idea', function () use ($requestClass) {
            app($requestClass);

            return response()->json(['ok' => true]);
        })->middleware('web');

        $this->actingAs(User::factory()->create())
            ->postJson('/_test/idea', ['title' => 'A'])
            ->assertForbidden();
    })->with([
        'store' => [StoreideaRequest::class],
        'update' => [UpdateideaRequest::class],
    ]);
})->group('feature', 'requests');
