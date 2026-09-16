<?php

declare(strict_types=1);

use App\Http\Requests\StoreIdeaRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;

covers(StoreIdeaRequest::class);

describe('idea form requests over HTTP', function (): void {
    it('allows authenticated submissions with valid data', function (): void {
        Route::post('/_test/idea', function () {
            app(StoreIdeaRequest::class);

            return response()->json(['ok' => true]);
        })->middleware('web');

        $this->actingAs(User::factory()->create())
            ->postJson('/_test/idea', ['title' => 'Learn guitar'])
            ->assertOk();
    });

    it('redirects back with errors and reopens the dialog on failure', function (): void {
        Route::post('/_test/idea', function () {
            app(StoreIdeaRequest::class);

            return response()->json(['ok' => true]);
        })->middleware('web');

        $this->actingAs(User::factory()->create())
            ->from('/ideas')
            ->post('/_test/idea', ['title' => 'A'])
            ->assertRedirect('/ideas')
            ->assertSessionHasErrors('title')
            ->assertSessionHas('open_modal', 'create-idea');
    });
})->group('feature', 'requests');
