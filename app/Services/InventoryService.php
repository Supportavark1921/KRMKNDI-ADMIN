<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function addStock(Product $product, int $qty, ?string $notes = null): void
    {
        DB::transaction(function () use ($product, $qty, $notes) {
            $inv = $product->inventory ?? Inventory::create(['product_id' => $product->id]);
            $inv->increment('total_stock', $qty);
            $inv->increment('available_stock', $qty);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'STOCK_ADDED',
                'quantity_change' => $qty,
                'notes' => $notes,
            ]);
        });
    }

    public function reserve(Product $product, int $qty, string $refType, int $refId): void
    {
        DB::transaction(function () use ($product, $qty, $refType, $refId) {
            $inv = $product->inventory()->lockForUpdate()->first();

            if (! $inv || $inv->available_stock < $qty) {
                throw new \RuntimeException("Insufficient stock for product {$product->id}.");
            }

            $inv->decrement('available_stock', $qty);
            $inv->increment('reserved_stock', $qty);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'ORDER_RESERVED',
                'quantity_change' => -$qty,
                'reference_type' => $refType,
                'reference_id' => $refId,
            ]);
        });
    }

    public function release(Product $product, int $qty, string $refType, int $refId): void
    {
        DB::transaction(function () use ($product, $qty, $refType, $refId) {
            $inv = $product->inventory()->lockForUpdate()->first();
            $inv->decrement('reserved_stock', $qty);
            $inv->increment('available_stock', $qty);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'ORDER_CANCELLED',
                'quantity_change' => $qty,
                'reference_type' => $refType,
                'reference_id' => $refId,
            ]);
        });
    }

    public function sell(Product $product, int $qty, string $refType, int $refId): void
    {
        DB::transaction(function () use ($product, $qty, $refType, $refId) {
            $inv = $product->inventory()->lockForUpdate()->first();
            $inv->decrement('reserved_stock', $qty);
            $inv->increment('sold_stock', $qty);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'ORDER_SOLD',
                'quantity_change' => -$qty,
                'reference_type' => $refType,
                'reference_id' => $refId,
            ]);
        });
    }

    public function adjust(Product $product, int $newTotal, ?string $notes = null): void
    {
        DB::transaction(function () use ($product, $newTotal, $notes) {
            $inv = $product->inventory()->lockForUpdate()->firstOrFail();
            $diff = $newTotal - $inv->total_stock;

            $inv->update([
                'total_stock' => $newTotal,
                'available_stock' => max(0, $inv->available_stock + $diff),
            ]);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'STOCK_ADJUSTED',
                'quantity_change' => $diff,
                'notes' => $notes,
            ]);
        });
    }
}
