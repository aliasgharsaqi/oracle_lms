<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\User;

class SchoolSeeder extends Seeder // This was the line with the error, now corrected.
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Step 1: Create the default school
        $school = School::firstOrCreate(
            ['email' => 'contact@oraclesacademy.com'],
            [
                'name' => 'Oracles Academy',
                'decription' => 'The first and primary institute using this platform.',
                'address' => '123 Education Lane, Knowledge City',
                'phone' => '111-222-3333',
                'website' => 'https://oraclesacademy.com',
                'subscription_plan' => 'premium',
                'status' => 'active',
            ]
        );

        // Step 2: Find the default School Admin user
        $schoolAdmin = User::where('email', 'admin@example.com')->first();

        // Step 3: Assign the school to the School Admin
        if ($school && $schoolAdmin) {
            $schoolAdmin->school_id = $school->id;
            $schoolAdmin->save();
            $this->command->info('Oracles Academy created and assigned to the default admin.');
        } else {
            $this->command->error('Could not find the default admin user to assign the school to.');
        }
    }
}

