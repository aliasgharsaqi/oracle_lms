<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // The order of seeders is critical to respect foreign key constraints.
        $this->call([
                        // 2. Users (including the admin/creator) must exist before they can be assigned.
            UserSeeder::class,

            // 1. Schools must be created first, as other records depend on it.
            SchoolSeeder::class,

            // 3. Roles and permissions are needed for users.
            RolePermissionSeeder::class,
            
            // 4. Semesters depend on a school_id.
            SemesterSeeder::class,

            // 5. SchoolClasses depend on school_id and created_by (user_id).
            SchoolClassSeeder::class,

            // 6. Subjects are dependent on a school_class_id.
            SubjectSeeder::class,

            StudentSeeder::class,
        ]);
    }
}

