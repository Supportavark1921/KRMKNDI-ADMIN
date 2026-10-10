@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp

<div class="store-header">
    <div>
        <h1 class="store-title">Order #{{ $order->id }}</h1>
        <p class="store-sub">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        @if(!$order->trashed())
            @can('samagri-orders.delete')
            <form method="POST" action="{{ route('admin.samagri-orders.destroy', $order) }}" style="display:inline">
                @csrf @method('DELETE')
                <button class="btn-secondary" onclick="return confirm('Archive this order?')">Archive</button>
            </form>
            @endcan
        @else
            @can('samagri-orders.restore')
            <form method="POST" action="{{ route('admin.samagri-orders.restore', $order->id) }}" style="display:inline">
                @csrf <button class="btn-secondary">Restore</button>
            </form>
            @endcan
        @endif
    </div>
</div>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
    {{-- Order Info + Booking Status --}}
    <div class="store-card">
        <h3 style="margin:0 0 12px">Order Info</h3>
        @php $color = match($order->status) {
            'confirmed','processing','shipped' => 'badge-pending',
            'delivered' => 'badge-active',
            'cancelled' => 'badge-inactive',
            default => 'badge-pending',
        }; @endphp
        <dl style="display:grid;grid-template-columns:120px 1fr;gap:6px 12px;margin:0 0 16px">
            <dt style="color:#888">Order ID</dt><dd>#{{ $order->id }}</dd>
            <dt style="color:#888">Status</dt><dd><span class="badge {{ $color }}">{{ ucfirst($order->status) }}</span></dd>
            <dt style="color:#888">Subtotal</dt><dd>₹{{ number_format($order->subtotal, 2) }}</dd>
            <dt style="color:#888">Total</dt><dd style="font-weight:600">₹{{ number_format($order->total_amount, 2) }}</dd>
            @if($order->notes)<dt style="color:#888">Notes</dt><dd>{{ $order->notes }}</dd>@endif
        </dl>
        @can('samagri-orders.update')
        @if(!in_array($order->status, ['cancelled','delivered']) && !$order->trashed())
        <div style="border-top:1px solid #eee;padding-top:14px">
            <p style="margin:0 0 8px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.04em">Update Booking Status</p>
            <form method="POST" action="{{ route('admin.samagri-orders.status', $order) }}" style="display:flex;gap:8px;align-items:center">
                @csrf
                <select name="status" class="form-input" style="height:36px;flex:1">
                    @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="btn-primary" style="height:36px;white-space:nowrap" onclick="return confirm('Update order status?')">Save Status</button>
            </form>
        </div>
        @endif
        @endcan
    </div>

    {{-- Payment --}}
    <div class="store-card">
        <h3 style="margin:0 0 12px">Payment</h3>
        @php $payStatus = $order->payment_status ?? 'unpaid';
        $pc = match($payStatus) { 'paid' => 'badge-active', 'screenshot_uploaded' => 'badge-pending', default => 'badge-inactive' }; @endphp
        <dl style="display:grid;grid-template-columns:140px 1fr;gap:6px 12px;margin:0 0 14px">
            <dt style="color:#888">Payment Status</dt><dd><span class="badge {{ $pc }}">{{ ucfirst(str_replace('_',' ',$payStatus)) }}</span></dd>
            <dt style="color:#888">Transaction ID</dt><dd style="font-family:ui-monospace,monospace;font-size:13px">{{ $order->payment_id ?: '—' }}</dd>
        </dl>

        @if($order->payment_screenshot)
        <div style="margin-bottom:14px">
            <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.04em">Payment Screenshot</p>
            <a href="{{ Storage::disk('public')->url($order->payment_screenshot) }}" target="_blank">
                <img src="{{ Storage::disk('public')->url($order->payment_screenshot) }}" style="max-height:200px;border-radius:8px;border:1px solid #e0e0e0;cursor:zoom-in">
            </a>
        </div>
        @endif

        @can('samagri-orders.update')
        <div style="border-top:1px solid #eee;padding-top:14px">
            <p style="margin:0 0 8px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.04em">Update Payment Status</p>
            <form method="POST" action="{{ route('admin.samagri-orders.payment', $order) }}" style="display:grid;gap:8px">
                @csrf @method('PATCH')
                <select name="payment_status" class="form-input" style="height:36px">
                    @foreach(['unpaid','screenshot_uploaded','paid','cancelled'] as $ps)
                    <option value="{{ $ps }}" @selected($payStatus === $ps)>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>
                    @endforeach
                </select>
                <input type="text" name="payment_id" class="form-input" style="height:36px" placeholder="Transaction / UTR ID" value="{{ old('payment_id', $order->payment_id) }}">
                <button class="btn-secondary" style="height:36px">Save Payment Status</button>
            </form>
        </div>
        @endcan
    </div>

    <div class="store-card">
        <h3 style="margin:0 0 12px">Customer</h3>
        <dl style="display:grid;grid-template-columns:120px 1fr;gap:6px 12px;margin:0">
            <dt style="color:#888">Name</dt><dd>{{ $order->name }}</dd>
            <dt style="color:#888">Phone</dt><dd>{{ $order->phone }}</dd>
            @if($order->address)<dt style="color:#888">Address</dt><dd style="white-space:pre-line">{{ $order->address }}</dd>@endif
            @if($order->user)<dt style="color:#888">Account</dt><dd>User #{{ $order->user->id }}</dd>@endif
        </dl>
    </div>
</div>

<div class="store-card">
    <h3 style="margin:0 0 12px">Items</h3>
    <table class="store-table">
        <thead><tr>
            <th>Product</th>
            <th>Unit</th>
            <th style="text-align:center">Qty</th>
            <th style="text-align:right">Unit price</th>
            <th style="text-align:right">Subtotal</th>
        </tr></thead>
        <tbody>
        @forelse($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td style="color:#888">{{ $item->unit ?? '—' }}</td>
            <td style="text-align:center">{{ $item->quantity }}</td>
            <td style="text-align:right">₹{{ number_format($item->price, 2) }}</td>
            <td style="text-align:right">₹{{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#888;padding:16px">No items.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight:600">
                <td colspan="4" style="text-align:right;padding-right:12px">Total</td>
                <td style="text-align:right">₹{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div style="margin-top:12px">
    <a href="{{ route('admin.samagri-orders.index') }}" class="btn-secondary">← Back to orders</a>
</div>

@endsection
