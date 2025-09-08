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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Step 1: Define all permissions
        $permissions = [
            'View Dashboard',
            'Manage Roles',
            'Add Role',
            'Edit Role',
            'Delete Role',
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
            'Manage Marks',
            'Manage Fees',
            'Manage Student Fees Plan',
            'Manage Collection of Fees',
            'Manage Reports',
            'View Paid Student Reports',
            'View Pending Student Reports',
            'View Monthly Income Reports',
            'View Total Income Reports',
        ];

        // Step 2: Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Step 3: Create Super Admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // Step 4: Create Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Step 5: Create Teacher role and assign specific permissions
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher', 'guard_name' => 'web']);
        $teacherPermissions = [
            'View Dashboard',
            'Manage Schedules',
            'Manage Marks',
            'View Admission',
        ];
        $teacherRole->syncPermissions($teacherPermissions);

        // Step 6: Create Staff role
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staffRole->givePermissionTo('View Dashboard');

        // Step 7: Assign roles to specific users
        // Assign Super Admin to user with ID 1
        $superAdminUser = User::find(1);
        if ($superAdminUser) {
            $superAdminUser->syncRoles($superAdminRole);
        }

        // Assign Admin to user with ID 2
        $adminUser = User::find(2);
        if ($adminUser) {
            $adminUser->syncRoles($adminRole);
        }

        $this->command->info('Roles and permissions have been seeded successfully!');
    }
}
