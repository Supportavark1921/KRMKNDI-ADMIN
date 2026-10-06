<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order): OrderResource|JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return new OrderResource($order->load('items'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'min:2', 'max:100'],
            'phone'             => ['required', 'string', 'min:10', 'max:30'],
            'address'           => ['nullable', 'string', 'max:500'],
            'notes'             => ['nullable', 'string', 'max:500'],
            'payment_method'    => ['required', 'in:cod,online'],
            'platform_fee'      => ['required', 'numeric', 'min:0'],
            'gst_amount'        => ['required', 'numeric', 'min:0'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.product_id'=> ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'  => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $productIds = collect($validated['items'])->pluck('product_id');
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Verify all products are available
        foreach ($validated['items'] as $line) {
            $product = $products->get($line['product_id']);
            if (! $product || ! $product->is_active) {
                return response()->json([
                    'message' => 'One or more products are unavailable.',
                ], 422);
            }
        }

        $order = DB::transaction(function () use ($validated, $products, $request) {
            $subtotal = 0.0;
            $lines    = [];

            foreach ($validated['items'] as $line) {
                $product  = $products->get($line['product_id']);
                $price    = (float) $product->price;
                $qty      = (int) $line['quantity'];
                $lineSub  = round($price * $qty, 2);
                $subtotal += $lineSub;

                $lines[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit'         => $product->unit,
                    'price'        => $price,
                    'quantity'     => $qty,
                    'subtotal'     => $lineSub,
                ];
            }

            $subtotal    = round($subtotal, 2);
            $platformFee = round((float) $validated['platform_fee'], 2);
            $gstAmount   = round((float) $validated['gst_amount'], 2);
            $totalAmount = round($subtotal + $platformFee + $gstAmount, 2);

            $order = Order::create([
                'user_id'        => $request->user()->id,
                'name'           => $validated['name'],
                'phone'          => $validated['phone'],
                'address'        => $validated['address'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'status'         => 'pending',
                'subtotal'       => $subtotal,
                'platform_fee'   => $platformFee,
                'gst_amount'     => $gstAmount,
                'total_amount'   => $totalAmount,
                'payment_method' => $validated['payment_method'],
            ]);

            $order->items()->createMany($lines);

            return $order;
        });

        return response()->json(
            new OrderResource($order->load('items')),
            201,
        );
    }
}
