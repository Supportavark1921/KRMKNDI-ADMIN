<?php

namespace Database\Seeders;

use App\Models\AvailabilitySlot;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        AvailabilitySlot::ensureWeek();

        $this->command->info('AvailabilitySeeder: weekly slots initialised (Mon–Sat 09:00–18:00, 60 min, Sunday off).');
    }
}
