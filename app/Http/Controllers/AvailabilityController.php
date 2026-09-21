<?php

namespace App\Http\Controllers;

use App\Models\AvailabilitySlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-appointments');
        AvailabilitySlot::ensureWeek();

        return view('availability.index', [
            'slots' => AvailabilitySlot::orderBy('weekday')->get(),
            'days' => [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');
        $data = $request->validate([
            'slots' => ['required', 'array', 'size:7'],
            'slots.*.is_available' => ['nullable', 'boolean'],
            'slots.*.start_time' => ['required', 'date_format:H:i'],
            'slots.*.end_time' => ['required', 'date_format:H:i', 'after:slots.*.start_time'],
            'slots.*.slot_minutes' => ['required', 'integer', 'in:30,45,60,90,120'],
        ]);

        foreach ($data['slots'] as $weekday => $slot) {
            AvailabilitySlot::updateOrCreate(['weekday' => $weekday], [
                'is_available' => isset($slot['is_available']),
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'slot_minutes' => $slot['slot_minutes'],
            ]);
        }

        return back()->with('success', 'Weekly availability has been saved.');
    }

    public function times(Request $request): JsonResponse
    {
        $data = $request->validate(['date' => ['required', 'date', 'after_or_equal:today']]);

        return response()->json(['times' => AvailabilitySlot::timesForDate($data['date'])]);
    }
}
