<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;

class CategoriesApiController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = ProductCategory::active()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'image' => $c->image ? asset('storage/'.$c->image) : null,
                'products_count' => $c->products_count,
            ]);

        return response()->json(['success' => true, 'data' => $categories]);
    }
}
