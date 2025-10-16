<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolClass;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // A structured array to map subjects to class names
        $subjectsByClass = [
            'Web Development' => [
                ['name' => 'HTML & CSS Fundamentals', 'subject_code' => 'WD-101', 'type' => 'core'],
                ['name' => 'JavaScript & DOM Manipulation', 'subject_code' => 'WD-102', 'type' => 'core'],
                ['name' => 'PHP with Laravel', 'subject_code' => 'WD-201', 'type' => 'core'],
                ['name' => 'UI/UX Designing Principles', 'subject_code' => 'WD-202', 'type' => 'optional'],
                ['name' => 'Backend Programming with APIs', 'subject_code' => 'WD-301', 'type' => 'core'],
            ],
            'Basic Computer Science' => [
                ['name' => 'Introduction to Computing', 'subject_code' => 'BCS-101', 'type' => 'core'],
                ['name' => 'Programming Fundamentals (Python)', 'subject_code' => 'BCS-102', 'type' => 'core'],
                ['name' => 'Operating Systems Concepts', 'subject_code' => 'BCS-201', 'type' => 'core'],
            ],
            'Data Structures & Algorithms' => [
                ['name' => 'Linear Data Structures', 'subject_code' => 'DSA-101', 'type' => 'core'],
                ['name' => 'Non-Linear Data Structures', 'subject_code' => 'DSA-102', 'type' => 'core'],
                ['name' => 'Algorithm Analysis & Design', 'subject_code' => 'DSA-201', 'type' => 'core'],
            ],
            'Database Management' => [
                ['name' => 'Relational Database Design (SQL)', 'subject_code' => 'DBM-101', 'type' => 'core'],
                ['name' => 'NoSQL Databases', 'subject_code' => 'DBM-102', 'type' => 'optional'],
            ],
            'Cyber Security Fundamentals' => [
                ['name' => 'Introduction to Cyber Security', 'subject_code' => 'CSF-101', 'type' => 'core'],
                ['name' => 'Network Security', 'subject_code' => 'CSF-102', 'type' => 'core'],
                ['name' => 'Ethical Hacking Principles', 'subject_code' => 'CSF-201', 'type' => 'optional'],
            ],
        ];

        // Loop through each class name
        foreach ($subjectsByClass as $className => $subjects) {
            // Find the class by its name to get the correct ID
            $class = SchoolClass::where('name', $className)->first();

            // If the class exists, create subjects for it
            if ($class) {
                foreach ($subjects as $subjectData) {
                    Subject::create([
                        'name' => $subjectData['name'],
                        'subject_code' => $subjectData['subject_code'],
                        'type' => $subjectData['type'],
                        'school_class_id' => $class->id, // Assign the correct class ID
                        'school_id' => 1,
                        'created_by' => 1, // Assuming admin user with ID 1
                        'active' => 1,
                    ]);
                }
            }
        }
    }
}
