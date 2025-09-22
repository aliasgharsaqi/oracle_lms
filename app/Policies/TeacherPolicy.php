<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
{
    use HandlesAuthorization;

    /**
     * This method runs before any other authorization checks.
     * If the user is a Super Admin, it grants them permission for any action.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->can('Manage Teachers');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Teacher $teacher)
    {
        return $user->can('Manage Teachers') && $user->school_id === $teacher->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->can('Add Teachers');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Teacher $teacher)
    {
        return $user->can('Edit Teachers') && $user->school_id === $teacher->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Teacher $teacher)
    {
        return $user->can('Delete Teachers') && $user->school_id === $teacher->school_id;
    }
}
