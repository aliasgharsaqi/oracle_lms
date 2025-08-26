<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('Admin')) {
            return true; // Super Admin bypasses all
        }
    }

    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['Admin']);
    }

    public function view(User $user, User $model)
    {
        return $user->hasAnyRole(['Admin']);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['Admin']);
    }

    public function update(User $user, User $model)
    {
        if ($user->hasRole('Admin')) {
            // Admins can only update Staff and Students
            return $model->hasAnyRole(['Staff', 'Student']);
        }

        return false; // Super Admin handled by before()
    }

    public function delete(User $user, User $model)
    {
        if ($user->hasRole('Admin')) {
            // Admins can only delete Staff and Students
            return $model->hasAnyRole(['Staff', 'Student']);
        }

        return false; // Super Admin handled by before()
    }
}
