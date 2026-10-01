<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NavamshaPanchangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PanchangController extends Controller
{
    public function __construct(private NavamshaPanchangService $service) {}

    /**
     * GET /api/v1/panchang
     *
     * Query params:
     *   latitude   float  required
     *   longitude  float  required
     *   timezone   float  required  (UTC offset, e.g. 5.5 for IST)
     *   date       string optional  Y-m-d, defaults to today
     *   feature    string optional  panchang_full|choghadiya|hora|rahu_kaal|sun_times|abhijit
     *   location   string optional  display name
     */
    public function show(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'timezone'  => ['required', 'numeric', 'between:-14,14'],
            'date'      => ['nullable', 'date_format:Y-m-d'],
            'feature'   => ['nullable', 'string', 'in:panchang_full,choghadiya,hora,rahu_kaal,sun_times,abhijit'],
            'location'  => ['nullable', 'string', 'max:120'],
        ]);

        $result = $this->service->getPanchang(
            latitude:     (float) $data['latitude'],
            longitude:    (float) $data['longitude'],
            timezone:     (float) $data['timezone'],
            date:         $data['date'] ?? today()->toDateString(),
            feature:      $data['feature'] ?? 'panchang_full',
            locationName: $data['location'] ?? '',
        );

        $status = $result['success'] ? 200 : 503;

        return response()->json($result, $status);
    }
}
