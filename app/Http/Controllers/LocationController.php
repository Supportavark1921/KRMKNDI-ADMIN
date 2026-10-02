<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Pincode;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function countries(): JsonResponse
    {
        return response()->json(
            Country::active()->orderBy('name')->get(['id', 'name', 'iso_code', 'phone_code', 'currency_code'])
        );
    }

    public function states(Country $country): JsonResponse
    {
        return response()->json(
            $country->states()->active()->orderBy('name')->get(['id', 'country_id', 'name', 'code', 'type'])
        );
    }

    public function statesByIso(string $iso): JsonResponse
    {
        $country = Country::where('iso_code', strtoupper($iso))->firstOrFail();

        return response()->json(
            $country->states()->active()->orderBy('name')->get(['id', 'country_id', 'name', 'code', 'type'])
        );
    }

    public function districts(State $state): JsonResponse
    {
        return response()->json(
            $state->districts()->active()->orderBy('name')->get(['id', 'state_id', 'name', 'code'])
        );
    }

    public function cities(District $district): JsonResponse
    {
        return response()->json(
            $district->cities()->active()->orderBy('name')->get(['id', 'district_id', 'state_id', 'name'])
        );
    }

    public function pincodes(City $city): JsonResponse
    {
        return response()->json(
            $city->pincodes()
                ->active()
                ->orderBy('pincode')
                ->orderBy('post_office_name')
                ->get(['id', 'pincode', 'post_office_name', 'office_type', 'delivery_status'])
        );
    }

    public function lookup(string $pincode): JsonResponse
    {
        $results = Pincode::active()
            ->byPincode($pincode)
            ->with(['state:id,name,code', 'district:id,name', 'city:id,name'])
            ->get(['id', 'pincode', 'post_office_name', 'office_type', 'state_id', 'district_id', 'city_id']);

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No records found for this PIN code.'], 404);
        }

        return response()->json($results);
    }

    public function search(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // PIN code search
        if (ctype_digit($q) && strlen($q) <= 6) {
            $rows = Pincode::active()
                ->where('pincode', 'like', $q.'%')
                ->with(['state:id,name,code', 'district:id,name'])
                ->limit(20)
                ->get(['id', 'pincode', 'post_office_name', 'state_id', 'district_id', 'city_id']);

            return response()->json($rows->map(fn ($p) => [
                'type' => 'pincode',
                'id' => $p->id,
                'label' => "{$p->pincode} — {$p->post_office_name}",
                'pincode' => $p->pincode,
                'post_office_name' => $p->post_office_name,
                'state' => $p->state?->name,
                'district' => $p->district?->name,
            ]));
        }

        // City / district text search
        $cities = City::active()
            ->where('name', 'like', "%{$q}%")
            ->with(['district:id,name', 'state:id,name,code'])
            ->limit(15)
            ->get(['id', 'name', 'district_id', 'state_id']);

        $districts = District::active()
            ->where('name', 'like', "%{$q}%")
            ->with(['state:id,name,code'])
            ->limit(10)
            ->get(['id', 'name', 'state_id']);

        $results = collect();

        foreach ($cities as $c) {
            $results->push([
                'type' => 'city',
                'id' => $c->id,
                'label' => "{$c->name}, {$c->district?->name}, {$c->state?->name}",
                'city' => $c->name,
                'district' => $c->district?->name,
                'state' => $c->state?->name,
                'state_code' => $c->state?->code,
            ]);
        }

        foreach ($districts as $d) {
            $results->push([
                'type' => 'district',
                'id' => $d->id,
                'label' => "{$d->name}, {$d->state?->name}",
                'district' => $d->name,
                'state' => $d->state?->name,
                'state_code' => $d->state?->code,
            ]);
        }

        return response()->json($results->values());
    }
}
