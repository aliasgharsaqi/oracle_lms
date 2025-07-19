<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service; // <-- Import the Service model

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'Event Promotions',
            'Content Creation & Reels',
            'Ticketing & Guestlist Management',
            'Artist & Influencer Collaborations',
            'Event Strategy & Planning',
        ];

        foreach ($services as $serviceName) {
            Service::create(['name' => $serviceName]);
        }
    }
}