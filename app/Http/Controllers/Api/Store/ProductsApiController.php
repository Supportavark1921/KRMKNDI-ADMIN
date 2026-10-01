<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'primaryImage', 'images', 'attributes', 'inventory', 'mataji'])
            ->active();

        if ($cat = $request->query('category')) {
            $query->where('category_id', $cat);
        }

        if ($type = $request->query('type')) {
            $query->where('product_type', $type);
        }

        if ($mataji = $request->query('mataji_id')) {
            $query->where('mataji_id', $mataji);
        }

        if ($min = $request->query('price_min')) {
            $query->where('price', '>=', $min);
        }

        if ($max = $request->query('price_max')) {
            $query->where('price', '<=', $max);
        }

        if ($s = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('short_description', 'like', "%{$s}%"));
        }

        $sort = match ($request->query('sort', 'latest')) {
            'price_asc'  => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'name'       => ['name', 'asc'],
            default      => ['created_at', 'desc'],
        };

        $products = $query->orderBy(...$sort)->paginate(20)->withQueryString();

        return response()->json([
            'success' => true,
            'data'    => $products->getCollection()->map(fn ($p) => $this->formatSummary($p)),
            'meta'    => [
                'current_page'  => $products->currentPage(),
                'last_page'     => $products->lastPage(),
                'per_page'      => $products->perPage(),
                'total'         => $products->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with(['category', 'images', 'attributes', 'inventory', 'mataji'])
            ->active()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->formatDetail($product),
        ]);
    }

    public function resale(Request $request): JsonResponse
    {
        $products = Product::with(['category', 'primaryImage', 'inventory', 'mataji'])
            ->resale()->active()->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $products->getCollection()->map(fn ($p) => $this->formatSummary($p)),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    // ── Formatters ────────────────────────────────────────────────────────────

    private function formatSummary(Product $p): array
    {
        return [
            'id'                => $p->id,
            'product_code'      => $p->product_code,
            'name'              => $p->name,
            'short_description' => $p->short_description,
            'product_type'      => $p->product_type,
            'price'             => (float) $p->price,
            'compare_at_price'  => $p->compare_at_price ? (float) $p->compare_at_price : null,
            'in_stock'          => $p->inventory?->isInStock() ?? false,
            'available_stock'   => $p->inventory?->available_stock ?? 0,
            'primary_image'     => $p->primaryImage?->url(),
            'category'          => ['id' => $p->category?->id, 'name' => $p->category?->name],
            'mataji'            => $p->mataji ? ['id' => $p->mataji->id, 'name' => $p->mataji->name] : null,
        ];
    }

    private function formatDetail(Product $p): array
    {
        return array_merge($this->formatSummary($p), [
            'sku'              => $p->sku,
            'description'      => $p->description,
            'brand_source'     => $p->brand_source,
            'offering_eligible'=> $p->offering_eligible,
            'resale_eligible'  => $p->resale_eligible,
            'images'           => $p->images->map(fn ($img) => [
                'id'         => $img->id,
                'url'        => $img->url(),
                'image_type' => $img->image_type,
                'is_primary' => $img->is_primary,
                'alt_text'   => $img->alt_text,
            ]),
            'attributes'       => $p->attributes->mapWithKeys(fn ($a) => [$a->key => $a->value]),
        ]);
    }
}
