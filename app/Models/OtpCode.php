<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpCode extends Model
{
    protected $fillable = ['phone', 'code', 'expires_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at'    => 'datetime',
        ];
    }

    /**
     * Generate a new OTP for the given phone number.
     * Deletes any existing unused codes for that phone first.
     */
    public static function generateFor(string $phone): self
    {
        static::where('phone', $phone)->whereNull('used_at')->delete();

        return static::create([
            'phone'      => $phone,
            'code'       => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    /**
     * Verify an OTP code for a phone number.
     * Returns false if not found, expired, or already used.
     * Marks the code as used on success.
     */
    public static function verifyFor(string $phone, string $code): bool
    {
        $otp = static::where('phone', $phone)
            ->where('code', $code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            return false;
        }

        $otp->update(['used_at' => now()]);

        return true;
    }
}
