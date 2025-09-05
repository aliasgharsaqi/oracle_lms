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
        // Step 1: Define all permissions
        $permissions = [
            'Manage User', 'Add User', 'Edit User', 'View User', 'Delete User', 'Manage Trash User',
            'Manage Classes', 'Add Classes', 'Edit Classes', 'Delete Classes',
            'Manage Teachers', 'Add Teachers', 'Edit Teachers', 'Delete Teachers',
            'Manage Subject', 'Add Subject', 'Edit Subject', 'Delete Subject', 'Manage Subject Status',
            'Manage Schedules', 'Add Schedules', 'Edit Schedules', 'Delete Schedules', 'View Schedules',
            'Manage Admission', 'Add Admission', 'Edit Admission', 'Delete Admission', 'View Admission',
            'Manage Marks', // ✅ Added
            'Manage Fees', 'Manage Student Fees Plan', 'Manage Collection of Fees',
            'Manage Reports', 'View Paid Student Reports', 'View Pending Student Reports',
            'View Monthly Income Reports', 'View Total Income Reports',
        ];

        // Step 2: Create permissions if not exists
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Step 3: Create Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Step 4: Assign all permissions to Admin role
        $adminRole->syncPermissions(Permission::all());

        // Step 5: Assign Admin role to specific users (id 1 and 2)
        $users = User::whereIn('id', [1, 2])->get();
        foreach ($users as $user) {
            $user->assignRole($adminRole);
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
