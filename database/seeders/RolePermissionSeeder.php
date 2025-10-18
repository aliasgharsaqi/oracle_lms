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

        // Permissions List
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
            'Manage Schools',
            'Manage Results',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin Role - Gets all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // School Admin Role (Previously 'Admin') - Gets all permissions EXCEPT managing schools and roles
        $schoolAdminRole = Role::firstOrCreate(['name' => 'School Admin', 'guard_name' => 'web']);
        $schoolAdminPermissions = Permission::where('name', '!=', 'Manage Schools')
            ->where('name', '!=', 'Manage Roles')->where('name', '!=', 'Manage User')
            ->get();
        $schoolAdminRole->syncPermissions($schoolAdminPermissions);

        // Teacher Role
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher', 'guard_name' => 'web']);
        $teacherRole->syncPermissions(['View Dashboard', 'Manage Schedules', 'Manage Marks', 'View Admission']);

        // Staff Role
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staffRole->givePermissionTo('View Dashboard');

        $studentRole = Role::firstOrCreate(['name' => 'Student', 'guard_name' => 'web']);
        $studentRole->givePermissionTo('View Dashboard');


        // Assign Roles to Users by Email (More reliable than ID)
        $superAdminUser = User::where('email', 'superadmin@example.com')->first();
        if ($superAdminUser) {
            $superAdminUser->syncRoles($superAdminRole);
        }

        $adminUser = User::where('email', 'oraclesacademy@example.com')->first();
        if ($adminUser) {
            $adminUser->syncRoles($schoolAdminRole);
        }

        $this->command->info('Roles and permissions have been seeded successfully!');
    }
}
