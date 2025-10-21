<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Semester;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the semester data
        $semesters = [
            [
                'name' => 'Fall 2024',
                'year' => 2024,
                'season' => 'Fall',
                'start_date' => Carbon::create(2024, 8, 15),
                'end_date' => Carbon::create(2024, 12, 20),
                'status' => 'inactive',
                'school_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Spring 2025',
                'year' => 2025,
                'season' => 'Spring',
                'start_date' => Carbon::create(2025, 1, 10),
                'end_date' => Carbon::create(2025, 5, 15),
                'status' => 'inactive',
                'school_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fall 2025',
                'year' => 2025,
                'season' => 'Fall',
                'start_date' => Carbon::create(2025, 8, 15),
                'end_date' => Carbon::create(2025, 12, 20),
                'status' => 'active', // Current semester
                'school_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'name' => 'Spring 2026',
                'year' => 2026,
                'season' => 'Spring',
                'start_date' => Carbon::create(2026, 1, 10),
                'end_date' => Carbon::create(2026, 5, 15),
                'status' => 'inactive',
                'school_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert the data into the database
        Semester::insert($semesters);
    }
}
