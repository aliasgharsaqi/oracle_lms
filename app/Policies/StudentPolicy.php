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
        if ($user->role === 'Super Admin') {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->role === 'Admin';
    }

    public function view(User $user, Student $student)
    {
        return $user->role === 'Admin';
    }

    public function create(User $user)
    {
        return $user->role === 'Admin';
    }

    public function update(User $user, Student $student)
    {
        return $user->role === 'Admin';
    }

    public function delete(User $user, Student $student)
    {
        return $user->role === 'Admin';
    }
}
