<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductCategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SamagriController extends Controller
{
    public function categories(): AnonymousResourceCollection
    {
        $categories = ProductCategory::active()
            ->roots()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'image']);

        return ProductCategoryResource::collection($categories);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'      => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'stock'       => ['nullable', 'string', 'in:in_stock,low_stock'],
            'sort'        => ['nullable', 'string', 'in:popular,price_asc,price_desc'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Product::with(['category', 'images', 'inventory'])
            ->active()
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('short_description', 'like', "%{$s}%");
            }))
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->stock === 'in_stock', fn ($q) => $q->whereHas('inventory', fn ($q) => $q->where('available_stock', '>', 10)))
            ->when($request->stock === 'low_stock', fn ($q) => $q->whereHas('inventory', fn ($q) => $q->whereBetween('available_stock', [1, 10])));

        $query = match ($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default      => $query->orderByDesc('reviews_count'),
        };

        return ProductResource::collection(
            $query->paginate($request->integer('per_page', 20))
        );
    }

    public function show(Product $product): ProductResource|JsonResponse
    {
        if ($product->status !== 'active') {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->load(['category', 'images', 'inventory']);

        return new ProductResource($product);
    }
}
