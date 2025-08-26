<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create some permissions
        $permissions = [
            'Manage User',
            'Add User',
            'Edit User',
            'View User',
            'Delete User',
            'Manage Trash User',
            'Manage Classes',
            'Add Classes',
            'Edit Classes',
            'Delete Classes',
            'Manage Teachers',
            'Add Teachers',
            'Edit Teachers',
            'Delete Teachers',
            'Manage Subject',
            'Add Subject',
            'Edit Subject',
            'Delete Subject',
            'Manage Subject Status',
            'Manage Schedules',
            'Add Schedules',
            'Edit Schedules',
            'Delete Schedules',
            'View Schedules',
            'Manage Admission',
            'Add Admission',
            'Edit Admission',
            'Delete Admission',
            'View Admission',
            'Manage Fees',
            'Manage Student Fees Plan',
            'Manage Collection of Fees',
            'Manage Reports',
            'View Paid Student Reports',
            'View Pending Student Reports',
            'View Monthly Income Reports',
            'View Total Income Reports',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Assign all permissions to Admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign Admin role to User with id=1
        $user = User::find(2);
        $users = User::find(1);
        if ($user) {
            $user->assignRole($adminRole);
             $users->assignRole($adminRole);
        }
    }
}
