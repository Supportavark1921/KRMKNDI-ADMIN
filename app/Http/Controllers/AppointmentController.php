<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AvailabilitySlot;
use App\Models\Service;
use App\Models\User;
use App\Notifications\AppointmentRequested;
use App\Notifications\AppointmentStatusChanged;
use App\Services\FcmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private readonly FcmService $fcm) {}
    public function index(): View
    {
        $user = auth()->user();

        $appointments = match ($user->role) {
            'admin', 'manager', 'support' => Appointment::with('user', 'guru')
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->get(),
            'guruji' => Appointment::with('user', 'guru')
                ->whereHas('guru', fn ($q) => $q->where('gurus.user_id', $user->id))
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->get(),
            default => $user->appointments()
                ->with('guru')
                ->orderByDesc('appointment_date')
                ->orderByDesc('appointment_time')
                ->get(),
        };

        return view('appointments.index', compact('appointments'));
    }

    public function show(Appointment $appointment): View
    {
        Gate::authorize('manage-appointments');
        $appointment->load(['user', 'guru', 'service']);

        return view('appointments.show', compact('appointment'));
    }

    public function create(): View
    {
        AvailabilitySlot::ensureWeek();

        $services = Service::active()->get();

        return view('appointments.create', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service' => ['required', 'string', 'max:120'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $isTaken = Appointment::whereDate('appointment_date', $data['appointment_date'])
            ->where('appointment_time', $data['appointment_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($isTaken) {
            return back()->withInput()->withErrors(['appointment_time' => 'That time slot has just been booked. Please choose another time.']);
        }

        if (! in_array($data['appointment_time'], AvailabilitySlot::timesForDate($data['appointment_date']), true)) {
            return back()->withInput()->withErrors(['appointment_time' => 'This slot is no longer available. Please choose another time.']);
        }

        $appointment = $request->user()->appointments()->create($data);
        $appointment->load('user');
        $request->user()->notify(new AppointmentRequested($appointment));
        User::where('role', 'admin')->get()->each->notify(new AppointmentRequested($appointment));

        $this->fcm->sendToUsers(
            [$request->user()->id],
            '📅 Appointment Requested',
            'Your appointment request has been received. We will confirm it shortly.',
            ['type' => 'appointment', 'id' => (string) $appointment->id],
        );

        return redirect()->route('appointments.index')->with('success', 'Your appointment request has been sent to Pandit Ji.');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate(['status' => ['required', 'in:pending,confirmed,completed,cancelled']]);
        $appointment->update($data);
        $appointment->load('user');
        $appointment->user->notify(new AppointmentStatusChanged($appointment));

        $messages = [
            'confirmed'  => 'Your appointment has been confirmed! ✅',
            'completed'  => 'Your appointment is now complete.',
            'cancelled'  => 'Your appointment has been cancelled.',
        ];
        if (isset($messages[$data['status']]) && $appointment->user_id) {
            $this->fcm->sendToUsers(
                [$appointment->user_id],
                '📅 Appointment Update',
                $messages[$data['status']],
                ['type' => 'appointment', 'id' => (string) $appointment->id],
            );
        }

        return back()->with('success', 'Appointment status updated.');
    }

    public function updatePayment(Request $request, Appointment $appointment): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'payment_status' => ['required', 'in:unpaid,screenshot_uploaded,paid,cancelled'],
            'payment_id'     => ['nullable', 'string', 'max:200'],
        ]);

        $appointment->update([
            'payment_status' => $data['payment_status'],
            'payment_id'     => $data['payment_id'] ?: $appointment->payment_id,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($appointment)
            ->withProperties(['payment_status' => $data['payment_status'], 'payment_id' => $data['payment_id']])
            ->log('payment_status_updated');

        if ($data['payment_status'] === 'paid' && $appointment->user_id) {
            $this->fcm->sendToUsers(
                [$appointment->user_id],
                '💳 Payment Confirmed',
                'Your appointment payment has been confirmed. We look forward to seeing you!',
                ['type' => 'appointment', 'id' => (string) $appointment->id],
            );
        } elseif ($data['payment_status'] === 'cancelled' && $appointment->user_id) {
            $this->fcm->sendToUsers(
                [$appointment->user_id],
                '❌ Payment Cancelled',
                'Your appointment payment could not be verified. Please contact us.',
                ['type' => 'appointment', 'id' => (string) $appointment->id],
            );
        }

        return back()->with('success', 'Payment status updated.');
    }
}
