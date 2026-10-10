<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Promotion extends Model
{
    use LogsActivity, SoftDeletes;

    public const TYPES = ['banner', 'promotion', 'announcement', 'offer'];

    public const PLACEMENTS = ['home_top', 'home_middle', 'shop', 'pooja', 'popup'];

    public const CTA_TYPES = ['none', 'product', 'category', 'mataji', 'pooja', 'services', 'donation', 'url', 'other'];

    public const AUDIENCES = ['all', 'user', 'guruji', 'vendor'];

    public const STATUSES = ['draft', 'active', 'inactive'];

    public const APPROVAL_STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'title', 'description', 'image', 'gallery',
        'type', 'placement', 'status',
        'cta_type', 'cta_value',
        'starts_at', 'ends_at',
        'sort_order', 'audience',
        'translations', 'created_by', 'updated_by',
        'approval_status',
    ];

    protected $casts = [
        'gallery' => 'array',
        'translations' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'type', 'placement', 'status', 'audience', 'starts_at', 'ends_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeLive(Builder $q): Builder
    {
        $now = now();

        return $q->where('status', 'active')
            ->where(fn ($s) => $s->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($s) => $s->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function scopeForAudience(Builder $q, string $role): Builder
    {
        return $q->where(fn ($s) => $s->where('audience', 'all')->orWhere('audience', $role));
    }

    public function scopeForPlacement(Builder $q, string $placement): Builder
    {
        return $q->where('placement', $placement);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function isLive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        $now = now();
        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }
        if ($this->ends_at && $this->ends_at->lt($now)) {
            return false;
        }

        return true;
    }

    public function translatedField(string $field, string $lang = 'en'): string
    {
        return $this->translations[$lang][$field] ?? $this->{$field} ?? '';
    }
}
