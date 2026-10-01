<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CityAdminController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-appointments');

        $query = City::with(['district', 'state'])->withCount('pincodes');

        if ($districtId = $request->query('district_id')) {
            $query->where('district_id', $districtId);
        } elseif ($stateId = $request->query('state_id')) {
            $query->where('state_id', $stateId);
        }

        if ($s = $request->query('search')) {
            $query->where('name', 'like', "%{$s}%");
        }

        return view('location.cities.index', [
            'cities'    => $query->orderBy('name')->paginate(50)->withQueryString(),
            'states'    => State::active()->orderBy('name')->get(['id', 'name', 'code']),
            'districts' => District::active()->orderBy('name')->get(['id', 'name', 'state_id']),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-appointments');
        return view('location.cities.create', [
            'states'    => State::active()->orderBy('name')->get(['id', 'name', 'code']),
            'districts' => collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'state_id'    => ['required', 'exists:states,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'name'        => ['required', 'string', 'max:150'],
            'status'      => ['required', 'in:active,inactive'],
        ]);

        City::create($data);

        return redirect()->route('admin.location.cities.index')->with('success', 'City added.');
    }

    public function edit(City $city): View
    {
        Gate::authorize('manage-appointments');
        return view('location.cities.edit', [
            'city'      => $city,
            'states'    => State::active()->orderBy('name')->get(['id', 'name', 'code']),
            'districts' => District::where('state_id', $city->state_id)->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, City $city): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'district_id' => ['required', 'exists:districts,id'],
            'name'        => ['required', 'string', 'max:150'],
            'status'      => ['required', 'in:active,inactive'],
        ]);

        $district = District::findOrFail($data['district_id']);
        $data['state_id'] = $district->state_id;

        $city->update($data);

        return redirect()->route('admin.location.cities.index')->with('success', 'City updated.');
    }
}
