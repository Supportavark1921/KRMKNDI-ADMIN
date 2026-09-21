<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AvailabilitySlot extends Model
{
    protected $fillable = ['weekday', 'is_available', 'start_time', 'end_time', 'slot_minutes'];

    protected function casts(): array
    {
        return ['is_available' => 'boolean'];
    }

    public static function ensureWeek(): void
    {
        foreach (range(1, 7) as $weekday) {
            static::firstOrCreate(
                ['weekday' => $weekday],
                ['is_available' => $weekday <= 6, 'start_time' => '09:00', 'end_time' => '18:00', 'slot_minutes' => 60],
            );
        }
    }

    public static function timesForDate(string $date): array
    {
        $day = Carbon::parse($date);
        $slot = static::where('weekday', $day->dayOfWeekIso)->first();

        if (! $slot || ! $slot->is_available) {
            return [];
        }

        $start = Carbon::parse($day->toDateString().' '.$slot->start_time);
        $end = Carbon::parse($day->toDateString().' '.$slot->end_time);
        $booked = Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
            ->all();
        $times = [];

        while ($start->lt($end)) {
            if ($start->isFuture() && ! in_array($start->format('H:i'), $booked, true)) {
                $times[] = $start->format('H:i');
            }
            $start->addMinutes($slot->slot_minutes);
        }

        return $times;
    }
}
