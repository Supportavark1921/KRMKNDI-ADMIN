<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pincode extends Model
{
    protected $fillable = [
        'country_id', 'state_id', 'district_id', 'city_id',
        'pincode', 'post_office_name', 'office_type', 'delivery_status',
        'division', 'region', 'circle', 'latitude', 'longitude', 'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeByPincode(Builder $q, string $pincode): Builder
    {
        return $q->where('pincode', $pincode);
    }
}
