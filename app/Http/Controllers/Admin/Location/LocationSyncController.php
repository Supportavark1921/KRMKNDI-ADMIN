<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Pincode;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LocationSyncController extends Controller
{
    public function show(): View
    {
        Gate::authorize('manage-appointments');

        $india = Country::where('iso_code', 'IN')->first();

        $stats = [
            'country' => $india?->name ?? '—',
            'states' => $india ? State::where('country_id', $india->id)->count() : 0,
            'districts' => $india ? District::whereHas('state', fn ($q) => $q->where('country_id', $india->id))->count() : 0,
            'cities' => $india ? City::whereHas('state', fn ($q) => $q->where('country_id', $india->id))->count() : 0,
            'pincodes' => $india ? Pincode::where('country_id', $india->id)->count() : 0,
        ];

        return view('location.sync', compact('stats'));
    }

    public function sync(): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $csvPath = storage_path('app/imports/india_pincodes.csv');

        if (! file_exists($csvPath)) {
            return redirect()->route('admin.location.sync')
                ->with('error', 'CSV file not found at storage/app/imports/india_pincodes.csv. Download it from data.gov.in first.');
        }

        // Run synchronously — for large CSVs run via CLI: php artisan location:import-india
        Artisan::call('location:import-india', ['--file' => $csvPath]);

        return redirect()->route('admin.location.sync')
            ->with('success', 'India postal data import complete. Stats updated below.');
    }
}
