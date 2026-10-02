<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CountryAdminController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-appointments');

        return view('location.countries.index', [
            'countries' => Country::withCount('states')->orderBy('name')->paginate(30),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-appointments');

        return view('location.countries.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'iso_code' => ['required', 'string', 'size:2', 'unique:countries,iso_code'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'currency_code' => ['nullable', 'string', 'max:5'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $data['iso_code'] = strtoupper($data['iso_code']);

        Country::create($data);

        return redirect()->route('admin.location.countries.index')->with('success', 'Country added.');
    }

    public function edit(Country $country): View
    {
        Gate::authorize('manage-appointments');

        return view('location.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'currency_code' => ['nullable', 'string', 'max:5'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $country->update($data);

        return redirect()->route('admin.location.countries.index')->with('success', 'Country updated.');
    }
}
