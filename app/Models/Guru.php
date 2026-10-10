<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'translations', 'image', 'background_image', 'gallery', 'status'];

    protected $casts = [
        'gallery' => 'array',
        'translations' => 'array',
    ];

    public function translatedField(string $field, string $lang = 'en'): string
    {
        if ($lang !== 'en' && isset($this->translations[$lang][$field])) {
            return $this->translations[$lang][$field];
        }

        return $this->{$field} ?? '';
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donationCategories(): BelongsToMany
    {
        return $this->belongsToMany(DonationCategory::class, 'guru_donation_categories', 'guru_id', 'category_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function activeCategories(): BelongsToMany
    {
        return $this->donationCategories()->wherePivot('status', 'active');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function guruServices(): HasMany
    {
        return $this->hasMany(GuruService::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function totalDonations(): float
    {
        return (float) $this->donations()->where('payment_status', 'success')->sum('donation_amount');
    }

    public function totalDonors(): int
    {
        return $this->donations()->where('payment_status', 'success')->distinct('user_id')->count('user_id');
    }

    public function totalTransactions(): int
    {
        return $this->donations()->where('payment_status', 'success')->count();
    }

    public function thisMonthDonations(): float
    {
        return (float) $this->donations()
            ->where('payment_status', 'success')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('donation_amount');
    }

    public function totalHandlingCharges(): float
    {
        return (float) $this->donations()->where('payment_status', 'success')->sum('handling_charge');
    }

    public function totalGst(): float
    {
        return (float) $this->donations()->where('payment_status', 'success')->sum('gst_amount');
    }
}
