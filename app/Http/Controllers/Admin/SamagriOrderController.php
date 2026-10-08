<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\FcmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SamagriOrderController extends Controller
{
    public function __construct(private readonly FcmService $fcm) {}
    private const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function index(Request $request): View
    {
        Gate::authorize('samagri-orders.view');

        $query = Order::with('user')->withTrashed();

        if ($s = $request->query('search')) {
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('id', $s)
            );
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.samagri-orders.index', [
            'orders'   => $query->latest()->paginate(25)->withQueryString(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(Order $samagriOrder): View
    {
        Gate::authorize('samagri-orders.view');
        $samagriOrder->load(['user', 'items']);

        return view('admin.samagri-orders.show', ['order' => $samagriOrder]);
    }

    public function updateStatus(Request $request, Order $samagriOrder): RedirectResponse
    {
        Gate::authorize('samagri-orders.update');

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
        ]);

        $from = $samagriOrder->status;
        $to   = $validated['status'];

        if (! $this->isValidTransition($from, $to)) {
            return back()->with('error', "Cannot move order from '{$from}' to '{$to}'.");
        }

        $samagriOrder->update(['status' => $to]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($samagriOrder)
            ->withProperties(['from' => $from, 'to' => $to])
            ->log('order_status_changed');

        $messages = [
            'confirmed'  => "Your order #{$samagriOrder->id} has been confirmed! ✅",
            'processing' => "Your order #{$samagriOrder->id} is being processed.",
            'shipped'    => "Your order #{$samagriOrder->id} has been shipped! 🚚",
            'delivered'  => "Your order #{$samagriOrder->id} has been delivered. 📦",
            'cancelled'  => "Your order #{$samagriOrder->id} has been cancelled.",
        ];
        if (isset($messages[$to]) && $samagriOrder->user_id) {
            $this->fcm->sendToUsers(
                [$samagriOrder->user_id],
                '🛍️ Order Update',
                $messages[$to],
                ['type' => 'samagri_order', 'id' => (string) $samagriOrder->id],
            );
        }

        return back()->with('success', "Order #" . $samagriOrder->id . " marked as {$to}.");
    }

    public function destroy(Order $samagriOrder): RedirectResponse
    {
        Gate::authorize('samagri-orders.delete');
        $samagriOrder->delete();

        return redirect()->route('admin.samagri-orders.index')->with('success', 'Order archived.');
    }

    public function restore(int $id): RedirectResponse
    {
        Gate::authorize('samagri-orders.restore');
        Order::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Order restored.');
    }

    private function isValidTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return false;
        }
        // Cancelled and delivered are terminal
        if (in_array($from, ['cancelled', 'delivered'], true)) {
            return false;
        }
        return true;
    }
}
