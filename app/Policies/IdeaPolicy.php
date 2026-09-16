<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IdeaPolicy
{
    /**
     * Determine whether the user owns the idea.
     */
    private function owns(User $user, Idea $idea): bool
    {
        return $idea->user_id === $user->id ?
            Response::allow() :
            Response::denyAsNotFound();;
    }
}
