<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** GET /v1/cart — return all cart items for the authenticated user */
    public function index(Request $request): JsonResponse
    {
        $items = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(fn ($ci) => [
                'id'       => $ci->id,
                'quantity' => $ci->quantity,
                'product'  => new ProductResource($ci->product),
            ]);

        return response()->json(['data' => $items]);
    }

    /** POST /v1/cart — add or increment a product */
    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        if (! $product->isAvailable()) {
            return response()->json(['message' => 'Product is unavailable.'], 422);
        }

        $qty  = $validated['quantity'] ?? 1;
        $item = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $qty);
            $item->refresh();
        } else {
            $item = CartItem::create([
                'user_id'    => $request->user()->id,
                'product_id' => $product->id,
                'quantity'   => $qty,
            ]);
        }

        return response()->json([
            'data' => [
                'id'       => $item->id,
                'quantity' => $item->quantity,
                'product'  => new ProductResource($item->product),
            ],
        ], 201);
    }

    /** PUT /v1/cart/{item} — set exact quantity (0 = remove) */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        if ($validated['quantity'] === 0) {
            $cartItem->delete();
            return response()->json(['data' => null]);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'data' => [
                'id'       => $cartItem->id,
                'quantity' => $cartItem->quantity,
                'product'  => new ProductResource($cartItem->product),
            ],
        ]);
    }

    /** DELETE /v1/cart/{item} — remove a single item */
    public function remove(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $cartItem->delete();
        return response()->json(['data' => null]);
    }

    /** DELETE /v1/cart — clear entire cart */
    public function clear(Request $request): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)->delete();
        return response()->json(['data' => []]);
    }
}
