<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'donation_id', 'user_id', 'guru_id', 'category_id',
        'donation_amount', 'handling_charge', 'gst_rate', 'gst_amount', 'total_amount',
        'currency', 'payment_status', 'payment_id', 'transaction_id', 'payment_method',
    ];

    const STATUSES = ['pending', 'success', 'failed', 'cancelled', 'refunded'];

    protected static function booted(): void
    {
        static::creating(function (Donation $d) {
            if (empty($d->donation_id)) {
                // Generate after we know the next id
                $next = (static::max('id') ?? 0) + 1;
                $d->donation_id = 'DON-'.str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DonationCategory::class, 'category_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSuccessful(Builder $q): Builder
    {
        return $q->where('payment_status', 'success');
    }

    public function scopeForStatus(Builder $q, string $status): Builder
    {
        return $q->where('payment_status', $status);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function statusColor(): string
    {
        return match ($this->payment_status) {
            'success' => 'green',
            'pending' => 'orange',
            'failed' => 'red',
            'cancelled' => 'gray',
            'refunded' => 'blue',
            default => 'gray',
        };
    }

    public function formattedAmount(): string
    {
        return '₹'.number_format($this->donation_amount, 2);
    }

    public function formattedTotal(): string
    {
        return '₹'.number_format($this->total_amount, 2);
    }
}
