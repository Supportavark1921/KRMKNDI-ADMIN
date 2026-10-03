<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles, permissions, default admin account
        $this->call([
            PermissionSeeder::class,
            CreateAdminSeeder::class,
        ]);

        // Services
        $this->call(ServicesSeeder::class);

        // Promotions
        $this->call(PromotionSeeder::class);

        // Location master data — safe to re-run (all use firstOrCreate)
        $this->call([
            IndiaLocationSeeder::class,           // Country + 36 States/UTs
            IndiaSampleDistrictCitySeeder::class, // ~58 districts, ~200 cities across 11 states
            IndiaPincodeSeeder::class,            // ~208 real India Post PIN codes
        ]);
    }
}
