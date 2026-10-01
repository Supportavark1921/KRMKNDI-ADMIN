<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Pincode;
use App\Models\State;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Import India Post / data.gov.in pincode directory CSV.
 *
 * Expected CSV columns (India Post format):
 *   CircleName, RegionName, DivisionName, OfficeName, Pincode, OfficeType,
 *   Delivery, District, StateName, Latitude, Longitude
 *
 * Source: https://data.gov.in/catalog/all-india-pincode-directory
 * Download the CSV and place it at: storage/app/imports/india_pincodes.csv
 */
class ImportIndiaPostalData extends Command
{
    protected $signature   = 'location:import-india
                                {--file= : Path to CSV file (default: storage/app/imports/india_pincodes.csv)}
                                {--dry-run : Parse only, no DB writes}';

    protected $description = 'Import / sync India Post pincode directory into the location master tables.';

    private int $newPincodes    = 0;
    private int $updatedPincodes = 0;
    private int $newCities      = 0;
    private int $newDistricts   = 0;

    public function handle(): int
    {
        $file = $this->option('file') ?? storage_path('app/imports/india_pincodes.csv');

        if (! file_exists($file)) {
            $this->error("CSV file not found: {$file}");
            $this->line('');
            $this->line('Download the All India Pincode Directory from:');
            $this->line('  https://data.gov.in/catalog/all-india-pincode-directory');
            $this->line("Place the CSV at: {$file}");
            return self::FAILURE;
        }

        $india = Country::where('iso_code', 'IN')->first();
        if (! $india) {
            $this->error('India not found in countries table. Run: php artisan db:seed --class=IndiaLocationSeeder');
            return self::FAILURE;
        }

        $isDry = (bool) $this->option('dry-run');

        $this->info($isDry ? '[DRY RUN] Parsing CSV...' : 'Importing India postal data...');
        $this->line("File: {$file}");

        // Pre-load states into memory for fast lookups
        $stateMap = State::where('country_id', $india->id)
            ->get()
            ->keyBy(fn ($s) => $this->normalise($s->name));

        $districtCache = [];
        $cityCache     = [];

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);
        $header = array_map('trim', $header);

        $bar   = $this->output->createProgressBar();
        $batch = [];
        $line  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count($row) < 5) {
                continue;
            }

            $cols = array_combine($header, array_map('trim', $row));

            $stateName       = $cols['StateName']    ?? $cols['State']    ?? '';
            $districtName    = $cols['District']     ?? '';
            $officeName      = $cols['OfficeName']   ?? $cols['Office']   ?? '';
            $pincode         = $cols['Pincode']      ?? $cols['Pincode']  ?? '';
            $officeType      = $cols['OfficeType']   ?? $cols['Type']     ?? null;
            $delivery        = $cols['Delivery']     ?? null;
            $circle          = $cols['CircleName']   ?? null;
            $region          = $cols['RegionName']   ?? null;
            $division        = $cols['DivisionName'] ?? null;
            $lat             = $cols['Latitude']     ?? null;
            $lng             = $cols['Longitude']    ?? null;

            if (! $pincode || ! $officeName || ! $stateName) {
                continue;
            }

            $pincode = preg_replace('/\D/', '', $pincode);
            if (strlen($pincode) !== 6) {
                continue;
            }

            if ($isDry) {
                $bar->advance();
                continue;
            }

            // Resolve state
            $stateKey = $this->normalise($stateName);
            $state = $stateMap[$stateKey] ?? null;
            if (! $state) {
                // Try fuzzy match
                $state = $stateMap->first(fn ($s) => str_contains($stateKey, $this->normalise($s->name)));
            }
            if (! $state) {
                continue; // unknown state — skip
            }

            // Resolve district
            $districtKey = $state->id . '::' . $this->normalise($districtName);
            if (! isset($districtCache[$districtKey])) {
                $district = District::firstOrCreate(
                    ['state_id' => $state->id, 'name' => $districtName],
                    ['status' => 'active']
                );
                if ($district->wasRecentlyCreated) {
                    $this->newDistricts++;
                }
                $districtCache[$districtKey] = $district->id;
            }
            $districtId = $districtCache[$districtKey];

            // Resolve city (use district name as city when no separate city info)
            $cityName    = $districtName;
            $cityKey     = $districtId . '::' . $this->normalise($cityName);
            if (! isset($cityCache[$cityKey])) {
                $city = City::firstOrCreate(
                    ['district_id' => $districtId, 'name' => $cityName],
                    ['state_id' => $state->id, 'status' => 'active']
                );
                if ($city->wasRecentlyCreated) {
                    $this->newCities++;
                }
                $cityCache[$cityKey] = $city->id;
            }
            $cityId = $cityCache[$cityKey];

            // Upsert pincode record
            $existing = Pincode::where('pincode', $pincode)
                ->where('post_office_name', $officeName)
                ->where('state_id', $state->id)
                ->first();

            $payload = [
                'country_id'       => $india->id,
                'state_id'         => $state->id,
                'district_id'      => $districtId,
                'city_id'          => $cityId,
                'office_type'      => $officeType,
                'delivery_status'  => $delivery,
                'circle'           => $circle,
                'region'           => $region,
                'division'         => $division,
                'latitude'         => is_numeric($lat) ? (float) $lat : null,
                'longitude'        => is_numeric($lng) ? (float) $lng : null,
                'status'           => 'active',
            ];

            if ($existing) {
                $existing->update($payload);
                $this->updatedPincodes++;
            } else {
                Pincode::create(array_merge($payload, [
                    'pincode'          => $pincode,
                    'post_office_name' => $officeName,
                ]));
                $this->newPincodes++;
            }

            $bar->advance();

            if ($line % 1000 === 0) {
                // Free memory for large datasets
                $districtCache = array_slice($districtCache, -500, null, true);
                $cityCache     = array_slice($cityCache, -500, null, true);
            }
        }

        fclose($handle);
        $bar->finish();
        $this->line('');

        if ($isDry) {
            $this->info("Dry run complete. {$line} rows parsed.");
            return self::SUCCESS;
        }

        $this->info('Import complete.');
        $this->table(
            ['Metric', 'Count'],
            [
                ['New PIN codes',     $this->newPincodes],
                ['Updated PIN codes', $this->updatedPincodes],
                ['New districts',     $this->newDistricts],
                ['New cities',        $this->newCities],
            ]
        );

        return self::SUCCESS;
    }

    private function normalise(string $s): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $s)));
    }
}
