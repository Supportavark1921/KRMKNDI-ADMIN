<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentStatusChanged extends Notification
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = ucfirst($this->appointment->status);

        return [
            'title' => 'Appointment '.$status,
            'message' => 'Your '.$this->appointment->service.' appointment on '.$this->appointment->appointment_date->format('d M Y').' is now '.$this->appointment->status.'.',
            'appointment_id' => $this->appointment->id,
            'url' => route('appointments.index'),
            'kind' => $this->appointment->status,
        ];
    }
}
