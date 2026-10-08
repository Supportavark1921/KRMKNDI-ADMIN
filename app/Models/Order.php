<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'phone', 'address',
        'status', 'subtotal', 'platform_fee', 'gst_amount', 'total_amount',
        'notes', 'payment_id', 'payment_method', 'payment_status', 'payment_screenshot',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'     => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'gst_amount'   => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
