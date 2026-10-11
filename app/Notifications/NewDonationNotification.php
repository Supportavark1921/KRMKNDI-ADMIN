<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDonationNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Donation $donation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $amount   = '₹'.number_format($this->donation->total_amount, 0);
        $guruName = $this->donation->guru->name ?? 'Guruji';
        $donor    = $this->donation->user->name ?? 'Guest';

        return [
            'title'   => 'New donation received',
            'message' => "{$donor} donated {$amount} to {$guruName}.",
            'url'     => route('donations.index'),
            'kind'    => 'success',
        ];
    }
}
