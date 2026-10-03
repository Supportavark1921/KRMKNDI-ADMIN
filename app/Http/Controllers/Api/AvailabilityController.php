<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AvailabilitySlot;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    /**
     * GET /api/availability/{month}
     *
     * Returns each day of the month with available flag and open slot count.
     * {month} format: YYYY-MM
     */
    public function month(string $month): JsonResponse
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json(['error' => 'Invalid month format. Use YYYY-MM.'], 422);
        }

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = $start->copy()->endOfMonth();
        $today = Carbon::today();

        $days = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $dateStr = $cursor->toDateString();
            $slots = ($cursor->gte($today))
                ? AvailabilitySlot::timesForDate($dateStr)
                : [];

            $days[] = [
                'date'        => $dateStr,
                'available'   => $cursor->gte($today) && count($slots) > 0,
                'slots_count' => count($slots),
            ];

            $cursor->addDay();
        }

        return response()->json(['data' => $days]);
    }

    /**
     * GET /api/availability/{date}/slots
     *
     * Returns the list of open time strings for a specific date.
     * {date} format: YYYY-MM-DD
     */
    public function slots(string $date): JsonResponse
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 422);
        }

        if (Carbon::parse($date)->lt(Carbon::today())) {
            return response()->json(['data' => []]);
        }

        $times = AvailabilitySlot::timesForDate($date);

        return response()->json(['data' => $times]);
    }
}
