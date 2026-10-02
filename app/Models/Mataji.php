<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Mataji extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'temple_name', 'status', 'offering_available'])->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'name', 'temple_name', 'address', 'city', 'state', 'pin_code',
        'image', 'description', 'contact_info', 'offering_available', 'status',
    ];

    protected $casts = [
        'contact_info' => 'array',
        'offering_available' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeOfferingAvailable(Builder $q): Builder
    {
        return $q->where('status', 'active')->where('offering_available', true);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
