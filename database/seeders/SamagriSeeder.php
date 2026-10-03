<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class SamagriSeeder extends Seeder
{
    public function run(): void
    {
        $samagriMap = [
            'Shobhagya Laxmi Poojan' => [
                ['name' => 'Kumkum & Haldi Pack',    'price' => 150,  'optional' => false],
                ['name' => 'Lotus Flowers (11 pcs)', 'price' => 300,  'optional' => false],
                ['name' => 'Ghee Diyas (set of 11)', 'price' => 250,  'optional' => false],
                ['name' => 'Panchamrit Kit',          'price' => 200,  'optional' => true],
                ['name' => 'Silver Coin Offering',   'price' => 500,  'optional' => true],
            ],
            'Mahavrat Kalp Anushthan' => [
                ['name' => 'Havan Samagri (1 kg)',   'price' => 450,  'optional' => false],
                ['name' => 'Ghee (500 ml)',           'price' => 300,  'optional' => false],
                ['name' => 'Sandalwood Sticks',       'price' => 200,  'optional' => false],
                ['name' => 'Copper Kalash',           'price' => 800,  'optional' => true],
                ['name' => 'Nav Dhanya Pack',         'price' => 350,  'optional' => true],
            ],
            'Lalita Sahastrachan' => [
                ['name' => 'Red Flowers (21 pcs)',   'price' => 200,  'optional' => false],
                ['name' => 'Kumkum & Sindoor Pack',  'price' => 150,  'optional' => false],
                ['name' => 'Panchamrit Kit',          'price' => 200,  'optional' => true],
                ['name' => 'Chunri (red)',            'price' => 400,  'optional' => true],
            ],
            'Lalita Astottar Pooja' => [
                ['name' => 'Red Flowers (11 pcs)',   'price' => 150,  'optional' => false],
                ['name' => 'Kumkum & Sindoor Pack',  'price' => 150,  'optional' => false],
                ['name' => 'Coconut Offering',       'price' => 100,  'optional' => true],
                ['name' => 'Panchamrit Kit',          'price' => 200,  'optional' => true],
            ],
            'Shree Yantra Abhishek' => [
                ['name' => 'Panchamrit Kit',          'price' => 200,  'optional' => false],
                ['name' => 'Gangajal (500 ml)',       'price' => 150,  'optional' => false],
                ['name' => 'Bilva Patra (21 leaves)', 'price' => 100,  'optional' => false],
                ['name' => 'Silver Abhishek Set',    'price' => 1200, 'optional' => true],
            ],
        ];

        $updated = 0;
        foreach ($samagriMap as $serviceName => $items) {
            $rows = Service::whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(translations, '$.en.name')) = ?",
                [$serviceName]
            )->get();

            foreach ($rows as $service) {
                $service->update(['pooja_samagri' => $items]);
                $updated++;
            }
        }

        $this->command->info("SamagriSeeder: pooja_samagri set on {$updated} service(s).");
    }
}
