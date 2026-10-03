<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFeeConfig extends Model
{
    protected $fillable = ['platform_fee', 'gst_rate', 'is_active'];

    protected function casts(): array
    {
        return [
            'platform_fee' => 'float',
            'gst_rate'     => 'float',
            'is_active'    => 'boolean',
        ];
    }

    /** Returns the single active config, creating defaults if none exists. */
    public static function active(): self
    {
        return static::where('is_active', true)->latest()->first()
            ?? static::create(['platform_fee' => 1.00, 'gst_rate' => 18.00, 'is_active' => true]);
    }

    /** Calculate fee breakdown for a booking amount. */
    public function calculate(float $bookingAmount): array
    {
        $fee = round($this->platform_fee, 2);
        $gst = round($fee * $this->gst_rate / 100, 2);

        return [
            'platform_fee'  => $fee,
            'gst_rate'      => $this->gst_rate,
            'gst_amount'    => $gst,
            'total_charges' => round($fee + $gst, 2),
            'grand_total'   => round($bookingAmount + $fee + $gst, 2),
        ];
    }
}
