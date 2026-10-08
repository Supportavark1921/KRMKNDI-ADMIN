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

        // ── Remove any guruji that is not Mayank (seed data only) ───────────
        Guru::where('name', '!=', 'Pt. Mayank')->each(fn ($g) => $g->delete());

        // ── Upsert Mayank ─────────────────────────────────────────────────────
        $mayank = Guru::updateOrCreate(
            ['name' => 'Pt. Mayank'],
            [
                'description' => 'Pandit Mayank is a devoted Mataji poojan specialist known for his deep knowledge of Shakti traditions and Navratri rituals. With years of experience conducting Shobhagya Laxmi Poojan and Mahavrat Kalp Anushthan, he brings sincerity and devotion to every ceremony he performs.',
                'status'      => 'active',
                'user_id'     => $mayankUser->id,
            ]
        );

        // Sync all donation categories to Mayank
        $catIds = collect($categoryModels)
            ->map(fn ($m) => $m->id)
            ->mapWithKeys(fn ($id) => [$id => ['status' => 'active']])
            ->toArray();

        $mayank->donationCategories()->sync($catIds);

        // ── Assign all services to Mayank ─────────────────────────────────────
        Service::query()->update(['guru_id' => $mayank->id]);

        $this->command->info('GurujiSeeder: Mayank set as sole Guruji with all services and donation categories.');
    }
}
