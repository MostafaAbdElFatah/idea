<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Step;
use App\Models\User;

class StepPolicy
{
    /**
     * Determine whether the user owns the idea.
     */
    private function owns(User $user,  Step $step): bool
    {
        return true; //$idea->user_id === $user->id;
    }
}
