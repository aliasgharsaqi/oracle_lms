<?php

namespace App\Policies;

use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentAttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow any user that passes the route's 'is_admin' middleware
        return $user->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentAttendance $studentAttendance): bool
    {
        // Allow any user that passes the route's 'is_admin' middleware
        return $user->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow any user that passes the route's 'is_admin' middleware
        return $user->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentAttendance $studentAttendance): bool
    {
        // Allow any user that passes the route's 'is_admin' middleware
        return $user->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentAttendance $studentAttendance): bool
    {
        // Allow any user that passes the route's 'is_admin' middleware
        return $user->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudentAttendance $studentAttendance): bool
    {
        // Allow any user that passes the route's 'is_admin' middleware
        return $user->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudentAttendance $studentAttendance): bool
    {
        // Allow any user that passes the route's 'is_admin' middleware
        return $user->exists();
    }
}