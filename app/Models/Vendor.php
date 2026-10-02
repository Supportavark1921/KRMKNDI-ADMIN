<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Vendor extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['business_name', 'status', 'approved_by'])->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'user_id', 'business_name', 'contact_person', 'phone', 'email',
        'address', 'city', 'state', 'gstin', 'pan', 'bank_details',
        'logo', 'status', 'admin_notes', 'approved_at', 'approved_by',
    ];

    protected $casts = [
        'bank_details' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
