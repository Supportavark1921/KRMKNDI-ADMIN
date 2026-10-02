<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\District;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DistrictAdminController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-appointments');

        $query = District::with('state.country')->withCount('cities');

        if ($stateId = $request->query('state_id')) {
            $query->where('state_id', $stateId);
        }

        if ($s = $request->query('search')) {
            $query->where('name', 'like', "%{$s}%");
        }

        return view('location.districts.index', [
            'districts' => $query->orderBy('name')->paginate(50)->withQueryString(),
            'states' => State::active()->orderBy('name')->get(['id', 'name', 'code']),
            'countries' => Country::active()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-appointments');

        return view('location.districts.create', [
            'states' => State::active()->orderBy('name')->get(['id', 'name', 'code', 'country_id']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'state_id' => ['required', 'exists:states,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        District::create($data);

        return redirect()->route('admin.location.districts.index')->with('success', 'District added.');
    }

    public function edit(District $district): View
    {
        Gate::authorize('manage-appointments');

        return view('location.districts.edit', [
            'district' => $district,
            'states' => State::active()->orderBy('name')->get(['id', 'name', 'code', 'country_id']),
        ]);
    }

    public function update(Request $request, District $district): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $district->update($data);

        return redirect()->route('admin.location.districts.index')->with('success', 'District updated.');
    }
}
