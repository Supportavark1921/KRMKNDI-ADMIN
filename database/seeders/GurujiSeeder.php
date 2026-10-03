<?php

namespace Database\Seeders;

use App\Models\DonationCategory;
use App\Models\Guru;
use App\Models\Service;
use Illuminate\Database\Seeder;

class GurujiSeeder extends Seeder
{
    public function run(): void
    {
        // ── Donation Categories ───────────────────────────────────────────────
        $categories = [
            [
                'name'        => 'Mataji Poojan',
                'description' => 'Complete Mataji Poojan with all rituals, offerings and aarti performed by the Guruji at the temple.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Navratri Anushthan',
                'description' => 'Nine-day Navratri Anushthan with daily havan, poojan and mantra jap dedicated in your name.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Griha Shanti Poojan',
                'description' => 'Vedic home peace ceremony to remove Vastu dosha and invite positive energy into your home.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Kaal Sarp Dosh Nivaran',
                'description' => 'Special poojan and havan to mitigate the effects of Kaal Sarp Dosh in your kundali.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Satyanarayan Katha',
                'description' => 'Full Satyanarayan Katha poojan for family well-being, prosperity and fulfillment of wishes.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Sundarkand Path',
                'description' => 'Recitation of Sundarkand from Ramcharitmanas for protection, strength and removal of obstacles.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Mangal Dosh Poojan',
                'description' => 'Remedial poojan to reduce the malefic effects of Mangal Dosh for marriage and health.',
                'status'      => 'active',
            ],
            [
                'name'        => 'Rudrabhishek',
                'description' => 'Sacred Rudrabhishek of Shivalinga with Panchamrit, Gangajal and Bilva patra for Shiva\'s blessings.',
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

        // ── Gurujis ───────────────────────────────────────────────────────────
        $gurujis = [
            [
                'name'        => 'Pt. Ramesh Sharma',
                'description' => 'Pandit Ramesh Sharma is a renowned Vedic astrologer and poojan specialist with over 25 years of experience. He has performed thousands of poojan ceremonies across India and is an expert in Navratri Anushthan, Griha Shanti and Kaal Sarp Dosh Nivaran rituals.',
                'status'      => 'active',
                'categories'  => ['Mataji Poojan', 'Navratri Anushthan', 'Griha Shanti Poojan', 'Kaal Sarp Dosh Nivaran'],
            ],
            [
                'name'        => 'Acharya Suresh Joshi',
                'description' => 'Acharya Suresh Joshi is a learned scholar of Sanskrit and Vedic tradition from Kashi. He specialises in Rudrabhishek, Satyanarayan Katha and Sundarkand recitation. His calm and devotional approach creates a deeply spiritual atmosphere for every ceremony.',
                'status'      => 'active',
                'categories'  => ['Satyanarayan Katha', 'Sundarkand Path', 'Rudrabhishek'],
            ],
            [
                'name'        => 'Pt. Dinesh Trivedi',
                'description' => 'Pandit Dinesh Trivedi is a certified Jyotishacharya with deep expertise in kundali analysis and Mangal Dosh remedies. He performs personalised poojan based on your birth chart to bring harmony in relationships, career and health.',
                'status'      => 'active',
                'categories'  => ['Mangal Dosh Poojan', 'Kaal Sarp Dosh Nivaran', 'Griha Shanti Poojan'],
            ],
            [
                'name'        => 'Pt. Gopal Das',
                'description' => 'Pandit Gopal Das comes from a family of temple priests with a lineage spanning four generations. He is well versed in Mataji poojan traditions and conducts all ceremonies with strict adherence to Vedic procedure and devotion.',
                'status'      => 'active',
                'categories'  => ['Mataji Poojan', 'Navratri Anushthan', 'Satyanarayan Katha', 'Sundarkand Path'],
            ],
        ];

        foreach ($gurujis as $guruData) {
            $cats = $guruData['categories'];
            unset($guruData['categories']);

            $guru = Guru::updateOrCreate(
                ['name' => $guruData['name']],
                $guruData
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
        // Services were rebuilt for multilingual — name/title no longer exist
        // as plain columns. Skip linking if neither column is present.
        $nameCol = \Illuminate\Support\Facades\Schema::hasColumn('services', 'name')
            ? 'name'
            : (\Illuminate\Support\Facades\Schema::hasColumn('services', 'title') ? 'title' : null);

        if ($nameCol) {
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
                    Service::where($nameCol, $serviceName)->update(['guru_id' => $guru->id]);
                }
            }
        }

        $this->command->info('GurujiSeeder: ' . count($gurujis) . ' gurujis and ' . count($categories) . ' donation categories inserted/updated.');
    }
}
