<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'price', 'status', 'product_type', 'category_id', 'vendor_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'product_code', 'sku', 'category_id', 'mataji_id', 'vendor_id',
        'name', 'short_description', 'description', 'brand_source',
        'unit', 'badge', 'rating', 'reviews_count',
        'uses', 'contents',
        'product_type', 'price', 'compare_at_price', 'status',
        'offering_eligible', 'resale_eligible',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'rating'           => 'decimal:2',
        'reviews_count'    => 'integer',
        'offering_eligible' => 'boolean',
        'resale_eligible'  => 'boolean',
        'uses'             => 'array',
        'contents'         => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $p) {
            if (empty($p->product_code)) {
                $p->product_code = 'PROD-'.strtoupper(substr(uniqid(), -6));
            }
        });

        static::created(function (self $p) {
            $p->inventory()->create([]);
        });
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function mataji(): BelongsTo
    {
        return $this->belongsTo(Mataji::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeOffering(Builder $q): Builder
    {
        return $q->where('product_type', 'MATAJI_OFFERING')->where('offering_eligible', true);
    }

    public function scopeResale(Builder $q): Builder
    {
        return $q->where('product_type', 'MATAJI_OFFERED_RESALE');
    }

    public function scopeNormalOnly(Builder $q): Builder
    {
        return $q->where('product_type', 'NORMAL');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->status === 'active' && ($this->inventory?->available_stock ?? 0) > 0;
    }
}
