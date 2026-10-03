<?php

namespace Database\Seeders;

use App\Models\DonationCategory;
use App\Models\Guru;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GurujiSeeder extends Seeder
{
    public function run(): void
    {
        // ── Donation Categories ───────────────────────────────────────────────
        $categories = [
            [
                'name'        => 'Anna Prasadam Seva',
                'description' => 'Contribute towards the sacred food offering (Anna Prasadam) served to devotees and the needy at the temple. Your donation feeds hundreds and earns divine blessings.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Bhog Naivedya Seva',
                'description' => 'Offer a Bhog Naivedya — a sacred food platter — to the deity in the name of you and your family. The Guruji performs the offering ritual with full Vedic procedure.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Vastra Prasadam',
                'description' => 'Donate sacred cloth (Vastra) to be offered to the deity as part of the daily shringar ritual. Vastra Seva brings prosperity and removes doshas from the family.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Siddh Vastue',
                'description' => 'Sponsor the preparation and energisation of a Siddh Vastu — a ritually charged item (yantra, rudraksha, or sacred object) blessed by the Guruji for your home or business.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Other Seva',
                'description' => 'Contribute to the general seva fund of the Guruji. Donations are used for temple maintenance, community programmes and charitable activities conducted in your name.',
                'status'      => 'active',
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = DonationCategory::updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }

        // ── Create/link user accounts for Gurujis ────────────────────────────
        $mayankUser = User::updateOrCreate(
            ['email' => 'mayank@krmkndi.com'],
            [
                'name'     => 'Pt. Mayank',
                'password' => Hash::make('Mayank@1234'),
                'role'     => 'guruji',
                'status'   => 'active',
            ]
        );
        $mayankUser->syncRoles(['guruji']);

        // ── Gurujis ───────────────────────────────────────────────────────────
        $gurujis = [
            [
                'name'        => 'Pt. Mayank',
                'description' => 'Pandit Mayank is a devoted Mataji poojan specialist known for his deep knowledge of Shakti traditions and Navratri rituals. With years of experience conducting Shobhagya Laxmi Poojan and Mahavrat Kalp Anushthan, he brings sincerity and devotion to every ceremony he performs.',
                'status'      => 'active',
                'user_id'     => $mayankUser->id,
                'categories'  => ['Anna Prasadam Seva', 'Bhog Naivedya Seva', 'Vastra Prasadam', 'Siddh Vastue', 'Other Seva'],
            ],
            [
                'name'        => 'Pt. Ramesh Sharma',
                'description' => 'Pandit Ramesh Sharma is a renowned Vedic astrologer and poojan specialist with over 25 years of experience. He has performed thousands of poojan ceremonies across India and is an expert in Navratri Anushthan, Griha Shanti and Kaal Sarp Dosh Nivaran rituals.',
                'status'      => 'active',
                'categories'  => ['Anna Prasadam Seva', 'Bhog Naivedya Seva', 'Vastra Prasadam', 'Siddh Vastue', 'Other Seva'],
            ],
            [
                'name'        => 'Acharya Suresh Joshi',
                'description' => 'Acharya Suresh Joshi is a learned scholar of Sanskrit and Vedic tradition from Kashi. He specialises in Rudrabhishek, Satyanarayan Katha and Sundarkand recitation. His calm and devotional approach creates a deeply spiritual atmosphere for every ceremony.',
                'status'      => 'active',
                'categories'  => ['Anna Prasadam Seva', 'Bhog Naivedya Seva', 'Vastra Prasadam', 'Siddh Vastue', 'Other Seva'],
            ],
            [
                'name'        => 'Pt. Dinesh Trivedi',
                'description' => 'Pandit Dinesh Trivedi is a certified Jyotishacharya with deep expertise in kundali analysis and Mangal Dosh remedies. He performs personalised poojan based on your birth chart to bring harmony in relationships, career and health.',
                'status'      => 'active',
                'categories'  => ['Anna Prasadam Seva', 'Bhog Naivedya Seva', 'Vastra Prasadam', 'Siddh Vastue', 'Other Seva'],
            ],
            [
                'name'        => 'Pt. Gopal Das',
                'description' => 'Pandit Gopal Das comes from a family of temple priests with a lineage spanning four generations. He is well versed in Mataji poojan traditions and conducts all ceremonies with strict adherence to Vedic procedure and devotion.',
                'status'      => 'active',
                'categories'  => ['Anna Prasadam Seva', 'Bhog Naivedya Seva', 'Vastra Prasadam', 'Siddh Vastue', 'Other Seva'],
            ],
        ];

        foreach ($gurujis as $guruData) {
            $cats = $guruData['categories'];
            unset($guruData['categories']);

            $guru = Guru::updateOrCreate(
                ['name' => $guruData['name']],
                array_filter($guruData, fn ($v) => $v !== null)
            );

            // Sync donation categories
            $catIds = collect($cats)
                ->map(fn ($name) => $categoryModels[$name]->id ?? null)
                ->filter()
                ->mapWithKeys(fn ($id) => [$id => ['status' => 'active']])
                ->toArray();

            $guru->donationCategories()->sync($catIds);
        }

        // ── Link services to Gurujis ─────────────────────────────────────────
        // Services store names in JSON translations column — match via JSON_EXTRACT.
        $serviceMap = [
            'Pt. Mayank'           => ['Shobhagya Laxmi Poojan', 'Mahavrat Kalp Anushthan'],
            'Acharya Suresh Joshi' => ['Lalita Sahastrachan', 'Lalita Astottar Pooja'],
            'Pt. Dinesh Trivedi'   => ['Shree Yantra Abhishek'],
            'Pt. Gopal Das'        => ['Shobhagya Laxmi Poojan', 'Lalita Astottar Pooja'],
        ];

        foreach ($serviceMap as $guruName => $serviceNames) {
            $guru = Guru::where('name', $guruName)->first();
            if (! $guru) {
                continue;
            }
            foreach ($serviceNames as $serviceName) {
                Service::whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(translations, '$.en.name')) = ?",
                    [$serviceName]
                )->update(['guru_id' => $guru->id]);
            }
        }

        $this->command->info('GurujiSeeder: ' . count($gurujis) . ' gurujis and ' . count($categories) . ' donation categories inserted/updated.');
    }
}
