<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Country extends Model
{
    protected $fillable = ['name', 'iso_code', 'phone_code', 'currency_code', 'status'];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
