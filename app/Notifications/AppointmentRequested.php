<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentRequested extends Notification
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isOwner = $notifiable->id === $this->appointment->user_id;

        return [
            'title' => $isOwner ? 'Appointment request received' : 'New appointment request',
            'message' => $isOwner
                ? 'Your '.$this->appointment->service.' request has been sent to Pandit Ji.'
                : $this->appointment->user->name.' requested '.$this->appointment->service.'.',
            'appointment_id' => $this->appointment->id,
            'url' => route('appointments.index'),
            'kind' => $isOwner ? 'booking' : 'request',
        ];
    }
}
