<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IdeaPolicy
{
    /**
     * Determine whether the user can list their ideas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the idea.
     */
    public function view(User $user, Idea $idea): Response
    {
        return $this->owns($user, $idea);
    }

    /**
     * Determine whether the user can create ideas.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the idea.
     */
    public function update(User $user, Idea $idea): Response
    {
        return $this->owns($user, $idea);
    }

    /**
     * Determine whether the user can delete the idea.
     */
    public function delete(User $user, Idea $idea): Response
    {
        return $this->owns($user, $idea);
    }

    /**
     * Ideas are not soft deleted, so they cannot be restored.
     */
    public function restore(User $user, Idea $idea): bool
    {
        return false;
    }

    /**
     * Ideas are not soft deleted, so they cannot be force deleted.
     */
    public function forceDelete(User $user, Idea $idea): bool
    {
        return false;
    }

    /**
     * Determine whether the user owns the idea.
     */
    private function owns(User $user, Idea $idea): Response
    {
        return $idea->user_id === $user->id
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
