<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\Module;
use App\Models\User;

class ModulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('modules.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Module $module): bool
    {
        return $user->can('modules.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Competition $competition): bool
    {
        if (! $user->can('modules.create')) {
            return false;
        }

        if ($user->hasRole('organizador')) {
            return $competition->admin_id === $user->id;
        }

        return true;

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Module $module): bool
    {
        if (! $user->can('modules.update')) {
            return false;
        }

        if ($user->hasRole('organizador')) {
            return $module->competition->admin_id === $user->id;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Module $module): bool
    {
        return $user->can('modules.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Module $module): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Module $module): bool
    {
        return false;
    }
}
