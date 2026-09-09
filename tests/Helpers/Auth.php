<?php

declare(strict_types=1);

use App\Models\User;

/**
 * Create a persisted user and authenticate the current test as that user.
 */
function loginAs(?User $user = null): User
{
    $user ??= User::factory()->create();
    test()->actingAs($user);

    return $user;
}
