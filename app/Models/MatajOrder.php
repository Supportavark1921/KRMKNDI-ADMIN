<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MatajOrder extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'mataji_orders';

    public const TYPES = ['sale', 'purchase'];

    public const STATUSES = ['draft', 'confirmed', 'paid', 'delivered', 'cancelled'];

    protected $fillable = [
        'guruji_id', 'mataji_id',
        'customer_user_id', 'customer_name', 'customer_phone',
        'type', 'status',
        'subtotal', 'discount', 'total',
        'notes', 'confirmed_at', 'paid_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total', 'type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    public function guruji(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guruji_id');
    }

    public function mataji(): BelongsTo
    {
        return $this->belongsTo(Mataji::class);
    }

    public function customerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MatajOrderItem::class, 'mataji_order_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('line_total');
        $this->update([
            'subtotal' => $subtotal,
            'total' => max(0, $subtotal - ($this->discount ?? 0)),
        ]);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function customerDisplayName(): string
    {
        return $this->customerUser?->name ?? $this->customer_name ?? 'Walk-in';
    }
}
