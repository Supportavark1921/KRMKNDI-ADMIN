<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductCategory extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'slug', 'parent_id', 'status'])->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $fillable = ['parent_id', 'guru_id', 'name', 'slug', 'description', 'image', 'sort_order', 'status'];

    protected static function booted(): void
    {
        static::creating(function (self $cat) {
            if (empty($cat->slug)) {
                $cat->slug = Str::slug($cat->name);
            }
        });
    }

    // Self-referential relations
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // All products including those in subcategories
    public function allProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id')
            ->orWhereIn('category_id', $this->children()->pluck('id'));
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeRoots(Builder $q): Builder
    {
        return $q->whereNull('parent_id');
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function isSubcategory(): bool
    {
        return ! is_null($this->parent_id);
    }

    // Display label like "Sarees & Vastras > Silk Sarees"
    public function getFullNameAttribute(): string
    {
        return $this->parent ? $this->parent->name.' > '.$this->name : $this->name;
    }
}
