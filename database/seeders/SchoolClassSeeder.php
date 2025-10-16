<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolClass;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classes = [
            [
                'name' => 'Web Development',
                'school_id' => 1,
                'created_by' => 1, // Assuming user with ID 1 is the admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Basic Computer Science',
                'school_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Data Structures & Algorithms',
                'school_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Database Management',
                'school_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'name' => 'Cyber Security Fundamentals',
                'school_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        SchoolClass::insert($classes);
    }
}
