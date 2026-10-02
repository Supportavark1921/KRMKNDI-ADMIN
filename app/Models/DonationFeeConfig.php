<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationFeeConfig extends Model
{
    protected $fillable = ['handling_charge', 'gst_rate', 'is_active'];

    protected function casts(): array
    {
        return [
            'handling_charge' => 'float',
            'gst_rate' => 'float',
            'is_active' => 'boolean',
        ];
    }

    /** Returns the single active config, creating defaults if none exists. */
    public static function active(): self
    {
        return static::where('is_active', true)->latest()->first()
            ?? static::create(['handling_charge' => 1.00, 'gst_rate' => 18.00, 'is_active' => true]);
    }

    /** Calculate the complete fee breakdown for a donation amount. */
    public function calculate(float $donationAmount): array
    {
        $handling = round($this->handling_charge, 2);
        $gst = round($handling * $this->gst_rate / 100, 2);
        $total = round($donationAmount + $handling + $gst, 2);

        return [
            'donation_amount' => round($donationAmount, 2),
            'handling_charge' => $handling,
            'gst_rate' => $this->gst_rate,
            'gst_amount' => $gst,
            'total_amount' => $total,
        ];
    }
}
