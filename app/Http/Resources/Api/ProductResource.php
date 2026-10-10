<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stock = $this->stockStatus();

        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'category'     => $this->category?->name,
            'category_id'  => $this->category_id,
            'description'  => $this->short_description,
            'detail'       => $this->description,
            'price'        => (float) $this->price,
            'mrp'          => $this->compare_at_price ? (float) $this->compare_at_price : null,
            'unit'         => $this->unit,
            'badge'        => $this->badge,
            'rating'       => (float) $this->rating,
            'reviews'      => $this->reviews_count,
            'stock'        => $stock,
            'uses'         => $this->uses ?? [],
            'contents'     => $this->contents ?? [],
            'image'        => $this->primaryImageUrl(),
            'images'       => $this->when(
                $request->routeIs('samagri.show'),
                fn () => $this->images->map(fn ($img) => Storage::disk('public')->url($img->path))->values()
            ),
        ];
    }

    private function stockStatus(): string
    {
        $available = $this->inventory?->available_stock ?? 0;

        if ($this->status === 'sold_out' || $available === 0) {
            return 'Out of stock';
        }

        return $available <= 10 ? 'Low stock' : 'In stock';
    }

    private function primaryImageUrl(): ?string
    {
        $primary = $this->relationLoaded('primaryImage')
            ? $this->primaryImage
            : $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        return $primary ? Storage::disk('public')->url($primary->path) : null;
    }
}
