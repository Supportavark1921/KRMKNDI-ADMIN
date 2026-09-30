<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCategory extends Model
{
    protected $fillable = ['name', 'description', 'image', 'status'];

    public function gurus(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_donation_categories', 'category_id', 'guru_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'category_id');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** Default categories to seed. */
    public static function defaults(): array
    {
        return [
            'Ann Prasadhan', 'Gau Seva', 'Ashram Seva', 'Sadhu Seva',
            'Education', 'Medical Help', 'Temple Seva', 'General Donation',
        ];
    }
}
