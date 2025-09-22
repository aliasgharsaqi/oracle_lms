<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * This method runs before any other authorization checks.
     * If the user is a Super Admin, it grants them permission for any action.
     *
     * @param \App\Models\User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the list of users.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('School Admin');
    }

    /**
     * Determine whether the user can view a specific user's profile.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function view(User $user, User $model)
    {
        return $user->hasRole('School Admin') && $user->school_id === $model->school_id;
    }

    /**
     * Determine whether the user can create new users.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole('School Admin');
    }

    /**
     * Determine whether the user can update a user's profile.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function update(User $user, User $model)
    {
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasRole('School Admin') && $user->school_id === $model->school_id;
    }

    /**
     * Determine whether the user can delete a user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function delete(User $user, User $model)
    {
        // Allow a School Admin to delete a user only if they are in the same school.
        return $user->hasRole('School Admin') && $user->school_id === $model->school_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        return $user->hasRole('School Admin') && $user->school_id === $model->school_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->hasRole('Super Admin') && $user->school_id === $model->school_id;
    }
}
