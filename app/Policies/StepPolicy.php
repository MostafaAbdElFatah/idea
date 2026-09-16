<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Step;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StepPolicy
{
    /**
     * Steps are only listed through their idea.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the step.
     */
    public function view(User $user, Step $step): Response
    {
        return $this->owns($user, $step);
    }

    /**
     * Steps are created through their idea.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the step.
     */
    public function update(User $user, Step $step): Response
    {
        return $this->owns($user, $step);
    }

    /**
     * Determine whether the user can delete the step.
     */
    public function delete(User $user, Step $step): Response
    {
        return $this->owns($user, $step);
    }

    /**
     * Steps are not soft deleted, so they cannot be restored.
     */
    public function restore(User $user, Step $step): bool
    {
        return false;
    }

    /**
     * Steps are not soft deleted, so they cannot be force deleted.
     */
    public function forceDelete(User $user, Step $step): bool
    {
        return false;
    }

    /**
     * Determine whether the user owns the step's idea.
     */
    private function owns(User $user, Step $step): Response
    {
        return $step->idea->user_id === $user->id
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
