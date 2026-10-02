<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mataji;
use App\Models\MatajOrder;
use App\Models\MatajOrderItem;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MatajOrderController extends Controller
{
    // ── List ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        Gate::authorize('mataji-orders.view');

        $user = auth()->user();
        $query = MatajOrder::with(['guruji', 'mataji', 'customerUser'])->withTrashed();

        // Guruji only sees own orders
        if ($user->isGuruji()) {
            $query->where('guruji_id', $user->id)->withoutTrashed();
        }

        if ($s = $request->query('search')) {
            $query->where(fn ($q) => $q->where('customer_name', 'like', "%{$s}%")->orWhere('customer_phone', 'like', "%{$s}%"));
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        return view('admin.mataji-orders.index', [
            'orders' => $query->latest()->paginate(20)->withQueryString(),
            'statuses' => MatajOrder::STATUSES,
            'types' => MatajOrder::TYPES,
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(): View
    {
        Gate::authorize('mataji-orders.create');

        return view('admin.mataji-orders.create', [
            'matajis' => Mataji::active()->orderBy('name')->get(),
            'products' => Product::active()->with('inventory')->orderBy('name')->get(),
            'types' => MatajOrder::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('mataji-orders.create');

        $data = $this->validateOrder($request);

        DB::transaction(function () use ($data, $request) {
            $user = auth()->user();

            $order = MatajOrder::create([
                'guruji_id' => $user->isGuruji() ? $user->id : $data['guruji_id'],
                'mataji_id' => $data['mataji_id'],
                'customer_user_id' => $data['customer_user_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'type' => $data['type'],
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'discount' => $data['discount'] ?? 0,
            ]);

            $this->syncItems($order, $request);
            $order->refresh()->load('items');
            $order->recalculate();
        });

        return redirect()->route('admin.mataji-orders.index')->with('success', 'Order created as draft.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(MatajOrder $matajOrder): View
    {
        Gate::authorize('mataji-orders.view');
        $this->authorizeOrder($matajOrder);
        $matajOrder->load(['guruji', 'mataji', 'customerUser', 'items.product']);

        return view('admin.mataji-orders.show', ['order' => $matajOrder]);
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(MatajOrder $matajOrder): View
    {
        Gate::authorize('mataji-orders.update');
        $this->authorizeOrder($matajOrder);
        abort_if(! $matajOrder->isDraft(), 403, 'Only draft orders can be edited.');

        $matajOrder->load('items.product');

        return view('admin.mataji-orders.edit', [
            'order' => $matajOrder,
            'matajis' => Mataji::active()->orderBy('name')->get(),
            'products' => Product::active()->with('inventory')->orderBy('name')->get(),
            'types' => MatajOrder::TYPES,
        ]);
    }

    public function update(Request $request, MatajOrder $matajOrder): RedirectResponse
    {
        Gate::authorize('mataji-orders.update');
        $this->authorizeOrder($matajOrder);
        abort_if(! $matajOrder->isDraft(), 403, 'Only draft orders can be edited.');

        $data = $this->validateOrder($request);

        DB::transaction(function () use ($matajOrder, $data, $request) {
            $matajOrder->update([
                'mataji_id' => $data['mataji_id'],
                'customer_user_id' => $data['customer_user_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'type' => $data['type'],
                'notes' => $data['notes'] ?? null,
                'discount' => $data['discount'] ?? 0,
            ]);

            $matajOrder->items()->delete();
            $this->syncItems($matajOrder, $request);
            $matajOrder->refresh()->load('items');
            $matajOrder->recalculate();
        });

        return redirect()->route('admin.mataji-orders.show', $matajOrder)->with('success', 'Order updated.');
    }

    // ── Confirm ───────────────────────────────────────────────────────────────

    public function confirm(MatajOrder $matajOrder): RedirectResponse
    {
        Gate::authorize('mataji-orders.update');
        $this->authorizeOrder($matajOrder);
        abort_if(! $matajOrder->isDraft(), 403, 'Order is not in draft status.');

        DB::transaction(function () use ($matajOrder) {
            $inv = app(InventoryService::class);
            $matajOrder->load('items.product');

            foreach ($matajOrder->items as $item) {
                if ($matajOrder->type === 'sale') {
                    // Reserve stock then mark sold
                    $inv->reserve($item->product, $item->quantity, "MatajOrder #{$matajOrder->id}");
                    $inv->sell($item->product, $item->quantity, "MatajOrder #{$matajOrder->id}");
                } else {
                    // Purchase = stock coming in
                    $inv->addStock($item->product, $item->quantity, "MatajOrder purchase #{$matajOrder->id}");
                }
            }

            $matajOrder->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        });

        return back()->with('success', 'Order confirmed and stock updated.');
    }

    // ── Cancel ────────────────────────────────────────────────────────────────

    public function cancel(MatajOrder $matajOrder): RedirectResponse
    {
        Gate::authorize('mataji-orders.update');
        $this->authorizeOrder($matajOrder);
        abort_if($matajOrder->isCancelled() || $matajOrder->status === 'delivered', 403, 'Cannot cancel this order.');

        DB::transaction(function () use ($matajOrder) {
            if ($matajOrder->isConfirmed() && $matajOrder->type === 'sale') {
                // Release reserved stock back
                $inv = app(InventoryService::class);
                $matajOrder->load('items.product');
                foreach ($matajOrder->items as $item) {
                    $inv->release($item->product, $item->quantity, "Cancelled MatajOrder #{$matajOrder->id}");
                }
            }
            $matajOrder->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Order cancelled.');
    }

    // ── Archive / Restore ─────────────────────────────────────────────────────

    public function destroy(MatajOrder $matajOrder): RedirectResponse
    {
        Gate::authorize('mataji-orders.delete');
        $this->authorizeOrder($matajOrder);
        $matajOrder->delete();

        return redirect()->route('admin.mataji-orders.index')->with('success', 'Order archived.');
    }

    public function restore(int $id): RedirectResponse
    {
        Gate::authorize('mataji-orders.restore');
        MatajOrder::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Order restored.');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function authorizeOrder(MatajOrder $order): void
    {
        $user = auth()->user();
        if ($user->isGuruji() && $order->guruji_id !== $user->id) {
            abort(403);
        }
    }

    private function validateOrder(Request $request): array
    {
        return $request->validate([
            'mataji_id' => ['required', 'integer', 'exists:matajis,id'],
            'guruji_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'in:sale,purchase'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'product_ids.*' => ['required', 'integer', 'exists:products,id'],
            'quantities.*' => ['required', 'integer', 'min:1'],
        ]);
    }

    private function syncItems(MatajOrder $order, Request $request): void
    {
        $productIds = $request->input('product_ids', []);
        $quantities = $request->input('quantities', []);

        foreach ($productIds as $i => $pid) {
            $qty = (int) ($quantities[$i] ?? 1);
            if ($qty < 1) {
                continue;
            }
            $product = Product::find($pid);
            if (! $product) {
                continue;
            }
            MatajOrderItem::create([
                'mataji_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $product->price,
                'line_total' => $product->price * $qty,
            ]);
        }
    }
}
