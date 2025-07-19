<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->role === 'Super Admin') {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return in_array($user->role, ['Super Admin', 'Admin']);
    }

    public function view(User $user, User $model)
    {
        return in_array($user->role, ['Super Admin', 'Admin']);
    }

    public function create(User $user)
    {
        return in_array($user->role, ['Super Admin', 'Admin']);
    }

    public function update(User $user, User $model)
    {
        if ($user->role === 'Admin') {
            // Admins can only update Staff and Students
            return in_array($model->role, ['Staff', 'Student']);
        }
        return false; // Super Admin is handled by before()
    }

    public function delete(User $user, User $model)
    {
        if ($user->role === 'Admin') {
            // Admins can only delete Staff and Students
            return in_array($model->role, ['Staff', 'Student']);
        }
        return false; // Super Admin is handled by before()
    }
}
