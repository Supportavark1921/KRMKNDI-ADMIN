<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Pincode;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PincodeAdminController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-appointments');

        $query = Pincode::with(['state', 'district', 'city']);

        if ($cityId = $request->query('city_id')) {
            $query->where('city_id', $cityId);
        } elseif ($districtId = $request->query('district_id')) {
            $query->where('district_id', $districtId);
        } elseif ($stateId = $request->query('state_id')) {
            $query->where('state_id', $stateId);
        }

        if ($s = $request->query('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('pincode', 'like', "%{$s}%")
                    ->orWhere('post_office_name', 'like', "%{$s}%");
            });
        }

        return view('location.pincodes.index', [
            'pincodes' => $query->orderBy('pincode')->paginate(50)->withQueryString(),
            'states' => State::active()->orderBy('name')->get(['id', 'name', 'code']),
            'districts' => District::active()->orderBy('name')->get(['id', 'name', 'state_id']),
            'cities' => City::active()->orderBy('name')->get(['id', 'name', 'district_id']),
        ]);
    }

    public function edit(Pincode $pincode): View
    {
        Gate::authorize('manage-appointments');

        return view('location.pincodes.edit', [
            'pincode' => $pincode->load(['state', 'district', 'city']),
            'cities' => City::where('district_id', $pincode->district_id)->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Pincode $pincode): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'post_office_name' => ['required', 'string', 'max:200'],
            'office_type' => ['nullable', 'string', 'max:50'],
            'delivery_status' => ['nullable', 'string', 'max:20'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $pincode->update($data);

        return redirect()->route('admin.location.pincodes.index')->with('success', 'PIN code updated.');
    }
}
