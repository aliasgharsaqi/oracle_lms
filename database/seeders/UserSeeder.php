<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create the Super Admin user. This user has NO school_id.
        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => 1,
            ]
        );

        // Create the default School Admin user.
        // The SchoolSeeder will assign a school_id to this user.
        User::firstOrCreate(
            ['email' => 'oraclesacademy@example.com'],
            [
                'name' => 'Oracle Admin',
                'password' => Hash::make('password'),
                'status' => 1,
            ]
        );
    }
}
