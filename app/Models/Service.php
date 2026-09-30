<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Service extends Model
{
    /** Languages the admin panel can assign to a service. */
    public const SUPPORTED_LANGUAGES = [
        'en' => 'English',
        'hi' => 'हिंदी',
        'mr' => 'मराठी',
        'gu' => 'ગુજરાતી',
        'ta' => 'தமிழ்',
        'te' => 'తెలుగు',
        'bn' => 'বাংলা',
    ];

    public const DEFAULT_LANGUAGE = 'en';

    protected $fillable = ['translations', 'images', 'pricing', 'status'];

    protected function casts(): array
    {
        return [
            'translations' => 'array',
            'images'       => 'array',
            'pricing'      => 'array',
        ];
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** Get a translation field for a language, falling back to the default. */
    public function translation(string $field, string $lang = self::DEFAULT_LANGUAGE): string
    {
        return $this->translations[$lang][$field]
            ?? $this->translations[self::DEFAULT_LANGUAGE][$field]
            ?? '';
    }

    public function name(string $lang = self::DEFAULT_LANGUAGE): string
    {
        return $this->translation('name', $lang);
    }

    public function title(string $lang = self::DEFAULT_LANGUAGE): string
    {
        return $this->translation('title', $lang);
    }

    public function description(string $lang = self::DEFAULT_LANGUAGE): string
    {
        return $this->translation('description', $lang);
    }

    /** Languages that have at least a name populated. */
    public function activeLanguages(): array
    {
        $t = $this->translations ?? [];
        return array_filter(array_keys($t), fn ($k) => !empty($t[$k]['name']));
    }

    public function primaryImage(): ?string
    {
        return $this->images['primary'] ?? null;
    }

    public function gallery(): array
    {
        return $this->images['gallery'] ?? [];
    }

    public function amount(): ?int
    {
        return $this->pricing['amount'] ?? null;
    }

    public function currency(): string
    {
        return $this->pricing['currency'] ?? 'INR';
    }

    public function discountAmount(): ?int
    {
        return $this->pricing['discount_amount'] ?? null;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeSearch(Builder $q, string $term): Builder
    {
        return $q->where(function ($q) use ($term) {
            $q->whereRaw("LOWER(JSON_EXTRACT(translations, '$.en.name')) LIKE ?", ['%' . strtolower($term) . '%'])
              ->orWhereRaw("LOWER(JSON_EXTRACT(translations, '$.hi.name')) LIKE ?", ['%' . strtolower($term) . '%']);
        });
    }
}
