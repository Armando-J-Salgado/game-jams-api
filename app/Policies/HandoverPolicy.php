<?php

namespace App\Policies;

use App\Models\Handover;
use App\Models\User;

class HandoverPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('handovers.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Handover $handover): bool
    {
        return $user->can('handovers.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('handovers.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Handover $handover): bool
    {
        return $user->can('handovers.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Handover $handover): bool
    {
        return $user->can('handovers.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Handover $handover): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Handover $handover): bool
    {
        return false;
    }
}
