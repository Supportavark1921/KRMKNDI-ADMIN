<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    public const TYPE_STATE = 'STATE';

    public const TYPE_UT = 'UNION_TERRITORY';

    protected $fillable = ['country_id', 'name', 'code', 'type', 'status'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function pincodes(): HasMany
    {
        return $this->hasMany(Pincode::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function isUnionTerritory(): bool
    {
        return $this->type === self::TYPE_UT;
    }

    public function typeLabel(): string
    {
        return $this->type === self::TYPE_UT ? 'Union Territory' : 'State';
    }
}
