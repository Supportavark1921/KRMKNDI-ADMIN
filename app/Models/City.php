<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class City extends Model
{
    protected $fillable = ['state_id', 'district_id', 'name', 'status'];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function pincodes(): HasMany
    {
        return $this->hasMany(Pincode::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }
}
