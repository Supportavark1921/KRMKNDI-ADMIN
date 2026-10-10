<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Article extends Model
{
    use LogsActivity, SoftDeletes;

    public const CATEGORIES = [
        'general', 'spiritual', 'astrology', 'puja', 'festival', 'tips',
    ];

    public const STATUSES = ['draft', 'published', 'archived'];

    public const APPROVAL_STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'cover_image',
        'category', 'tags', 'status', 'published_at',
        'author_id', 'translations', 'created_by', 'updated_by',
        'approval_status',
    ];

    protected $casts = [
        'tags' => 'array',
        'translations' => 'array',
        'published_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'category', 'status', 'published_at', 'author_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
            ->where(fn ($s) => $s->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeSearch(Builder $q, string $term): Builder
    {
        return $q->where(fn ($s) => $s
            ->where('title', 'like', "%{$term}%")
            ->orWhere('excerpt', 'like', "%{$term}%"));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    public function translatedField(string $field, string $lang = 'en'): string
    {
        if ($lang !== 'en' && isset($this->translations[$lang][$field])) {
            return $this->translations[$lang][$field];
        }

        return $this->{$field} ?? '';
    }
}
