<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // --- 1. Define which classes to add students to ---
        $classNames = [
            'Web Development',
            'Basic Computer Science',
            'Data Structures & Algorithms',
            'Database Management',
            'Cyber Security Fundamentals'
        ];

        // --- 2. Fetch the class models from the database ---
        $classes = SchoolClass::whereIn('name', $classNames)->get();

        // --- 3. Create one student for each of the 5 classes ---
        $studentCount = 1;
        foreach ($classes as $class) {
            $this->createStudent($class, 'Student ' . $studentCount++);
        }

        // --- 4. Create two additional students for the Web Development class ---
        $webDevClass = $classes->firstWhere('name', 'Web Development');
        if ($webDevClass) {
            $this->createStudent($webDevClass, 'Student ' . $studentCount++);
            $this->createStudent($webDevClass, 'Student ' . $studentCount++);
        }
    }

    /**
     * Helper function to create a user and a student record.
     *
     * @param SchoolClass $class
     * @param string $name
     * @return void
     */
    private function createStudent(SchoolClass $class, string $name)
    {
        // Create the User record first
        $user = User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@oraclesacademy.com',
            'password' => Hash::make('password'),
            'school_id' => 1,
            'phone' => '123-456-7890',
            'address' => '123 Academy Lane',
        ]);

        // Assign the 'student' role to the user
        $user->assignRole('student');

        // Create the associated Student record
        Student::create([
            'user_id' => $user->id,
            'father_name' => 'Father of ' . $name,
            'id_card_number' => 'ID-' . time() . $user->id,
            'father_phone' => '098-765-4321',
            'address' => $user->address,
            'school_id' => $class->school_id,
            'school_class_id' => $class->id,
            'section' => 'A',
        ]);
    }
}
