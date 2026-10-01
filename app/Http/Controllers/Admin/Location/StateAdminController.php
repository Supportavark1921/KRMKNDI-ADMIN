<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StateAdminController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-appointments');

        $query = State::with('country')->withCount('districts');

        if ($countryId = $request->query('country_id')) {
            $query->where('country_id', $countryId);
        }

        if ($s = $request->query('search')) {
            $query->where('name', 'like', "%{$s}%");
        }

        return view('location.states.index', [
            'states'    => $query->orderBy('name')->paginate(40)->withQueryString(),
            'countries' => Country::active()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-appointments');
        return view('location.states.create', [
            'countries' => Country::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'name'       => ['required', 'string', 'max:150'],
            'code'       => ['nullable', 'string', 'max:10'],
            'type'       => ['required', 'in:STATE,UNION_TERRITORY'],
            'status'     => ['required', 'in:active,inactive'],
        ]);

        State::create($data);

        return redirect()->route('admin.location.states.index')->with('success', 'State/UT added.');
    }

    public function edit(State $state): View
    {
        Gate::authorize('manage-appointments');
        return view('location.states.edit', [
            'state'     => $state,
            'countries' => Country::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, State $state): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:150'],
            'code'   => ['nullable', 'string', 'max:10'],
            'type'   => ['required', 'in:STATE,UNION_TERRITORY'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $state->update($data);

        return redirect()->route('admin.location.states.index')->with('success', 'State/UT updated.');
    }
}
