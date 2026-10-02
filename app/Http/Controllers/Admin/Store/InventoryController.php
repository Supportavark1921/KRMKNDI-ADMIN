<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        Gate::authorize('access-store-admin');

        return view('admin.store.inventory.index', [
            'products' => Product::with('inventory')->active()->latest()->paginate(30),
        ]);
    }

    public function addStock(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('access-store-admin');

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:300'],
        ]);

        app(InventoryService::class)->addStock($product, $data['quantity'], $data['notes'] ?? null);

        return back()->with('success', "Added {$data['quantity']} units to {$product->name}.");
    }

    public function adjust(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('access-store-admin');

        $data = $request->validate([
            'new_total' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:300'],
        ]);

        app(InventoryService::class)->adjust($product, $data['new_total'], $data['notes'] ?? null);

        return back()->with('success', "Stock adjusted for {$product->name}.");
    }

    public function history(Product $product): View
    {
        Gate::authorize('access-store-admin');
        $product->load('inventory');

        return view('admin.store.inventory.history', [
            'product' => $product,
            'transactions' => $product->inventoryTransactions()->with('actor')->latest()->paginate(40),
        ]);
    }
}
