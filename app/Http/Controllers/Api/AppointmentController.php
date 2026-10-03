<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\BookingFeeConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * POST /api/appointments
     * Create a new booking. Requires auth:sanctum.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'guru_id'          => ['required', 'integer', 'exists:gurus,id'],
            'service_id'       => ['required', 'integer', 'exists:services,id'],
            'service_name'     => ['required', 'string', 'max:200'],
            'name'             => ['required', 'string', 'max:100'],
            'phone'            => ['required', 'string', 'max:30'],
            'appointment_date' => ['required', 'date_format:Y-m-d'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'total_amount'     => ['required', 'numeric', 'min:0'],
            'samagri'          => ['nullable', 'array'],
            'samagri.*.name'   => ['required', 'string'],
            'samagri.*.price'  => ['required', 'numeric'],
        ]);

        // Verify slot is still free
        $taken = Appointment::where('appointment_date', $data['appointment_date'])
            ->where('appointment_time', $data['appointment_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($taken) {
            return response()->json([
                'success' => false,
                'error'   => ['code' => 'SLOT_TAKEN', 'message' => 'This slot was just booked. Please choose another time.'],
            ], 409);
        }

        $appointment = Appointment::create([
            'user_id'          => $request->user()->id,
            'guru_id'          => $data['guru_id'],
            'service_id'       => $data['service_id'],
            'service'          => $data['service_name'],
            'name'             => $data['name'],
            'phone'            => $data['phone'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'total_amount'     => $data['total_amount'],
            'samagri'          => $data['samagri'] ?? [],
            'status'           => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'data'    => $this->format($appointment),
        ], 201);
    }

    /**
     * GET /api/appointments
     * List the authenticated user's bookings, newest first.
     */
    public function index(Request $request): JsonResponse
    {
        $appointments = Appointment::where('user_id', $request->user()->id)
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $appointments->map(fn ($a) => $this->format($a))->values(),
        ]);
    }

    private function format(Appointment $a): array
    {
        return [
            'id'               => $a->id,
            'service'          => $a->service,
            'name'             => $a->name,
            'phone'            => $a->phone,
            'appointment_date' => $a->appointment_date?->format('Y-m-d'),
            'appointment_time' => $a->appointment_time,
            'total_amount'     => (float) $a->total_amount,
            'status'           => $a->status,
            'samagri'          => $a->samagri ?? [],
            'created_at'       => $a->created_at?->toIso8601String(),
        ];
    }
}
