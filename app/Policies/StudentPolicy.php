<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('Admin')) {
            return true; // Super Admin bypasses all checks
        }
    }

    public function viewAny(User $user)
    {
        return $user->hasRole('Admin');
    }

    public function view(User $user, Student $student)
    {
        return $user->hasRole('Admin');
    }

    public function create(User $user)
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Student $student)
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Student $student)
    {
        return $user->hasRole('Admin');
    }
}
