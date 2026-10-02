@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')

<div class="store-header">
    <div>
        <h1 class="store-title">Order #{{ $order->id }}</h1>
        <p class="store-sub">{{ ucfirst($order->type) }} · {{ ucfirst($order->status) }}</p>
    </div>
    <div style="display:flex;gap:8px">
        @if($order->isDraft())
            @can('mataji-orders.update')
            <a href="{{ route('admin.mataji-orders.edit', $order) }}" class="btn-secondary">Edit</a>
            <form method="POST" action="{{ route('admin.mataji-orders.confirm', $order) }}" style="display:inline">
                @csrf <button class="btn-primary" onclick="return confirm('Confirm and update stock?')">Confirm Order</button>
            </form>
            @endcan
        @endif
        @if(!$order->isCancelled() && !$order->trashed() && $order->status !== 'delivered')
            @can('mataji-orders.update')
            <form method="POST" action="{{ route('admin.mataji-orders.cancel', $order) }}" style="display:inline">
                @csrf <button class="btn-danger" onclick="return confirm('Cancel this order?')">Cancel</button>
            </form>
            @endcan
        @endif
        @if(!$order->trashed())
            @can('mataji-orders.delete')
            <form method="POST" action="{{ route('admin.mataji-orders.destroy', $order) }}" style="display:inline">
                @csrf @method('DELETE')
                <button class="btn-secondary" onclick="return confirm('Archive this order?')">Archive</button>
            </form>
            @endcan
        @else
            @can('mataji-orders.restore')
            <form method="POST" action="{{ route('admin.mataji-orders.restore', $order->id) }}" style="display:inline">
                @csrf <button class="btn-secondary">Restore</button>
            </form>
            @endcan
        @endif
    </div>
</div>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
    <div class="store-card">
        <h3 style="margin:0 0 12px">Order Info</h3>
        <dl style="display:grid;grid-template-columns:140px 1fr;gap:6px 12px;margin:0">
            <dt style="color:#888">Guruji</dt><dd>{{ $order->guruji?->name ?? '—' }}</dd>
            <dt style="color:#888">Mataji</dt><dd>{{ $order->mataji?->name ?? '—' }} ({{ $order->mataji?->temple_name }})</dd>
            <dt style="color:#888">Type</dt><dd>{{ ucfirst($order->type) }}</dd>
            <dt style="color:#888">Status</dt><dd>{{ ucfirst($order->status) }}</dd>
            @if($order->confirmed_at)<dt style="color:#888">Confirmed</dt><dd>{{ $order->confirmed_at->format('d M Y H:i') }}</dd>@endif
            @if($order->paid_at)<dt style="color:#888">Paid</dt><dd>{{ $order->paid_at->format('d M Y H:i') }}</dd>@endif
            @if($order->delivered_at)<dt style="color:#888">Delivered</dt><dd>{{ $order->delivered_at->format('d M Y H:i') }}</dd>@endif
            @if($order->notes)<dt style="color:#888">Notes</dt><dd>{{ $order->notes }}</dd>@endif
        </dl>
    </div>

    <div class="store-card">
        <h3 style="margin:0 0 12px">Customer</h3>
        <dl style="display:grid;grid-template-columns:140px 1fr;gap:6px 12px;margin:0">
            <dt style="color:#888">Name</dt><dd>{{ $order->customerDisplayName() }}</dd>
            <dt style="color:#888">Phone</dt><dd>{{ $order->customer_phone ?? '—' }}</dd>
            @if($order->customerUser)<dt style="color:#888">Account</dt><dd>{{ $order->customerUser->email }}</dd>@endif
        </dl>
    </div>
</div>

<div class="store-card">
    <h3 style="margin:0 0 12px">Line Items</h3>
    <table class="store-table">
        <thead><tr><th>Product</th><th>Qty</th><th>Unit price</th><th style="text-align:right">Line total</th></tr></thead>
        <tbody>
        @forelse($order->items as $item)
        <tr>
            <td>{{ $item->product?->name ?? '#' . $item->product_id }}</td>
            <td>{{ $item->quantity }}</td>
            <td>₹{{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align:right">₹{{ number_format($item->line_total, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#888;padding:16px">No items.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr><td colspan="3" style="text-align:right;padding-right:12px">Subtotal</td><td style="text-align:right">₹{{ number_format($order->subtotal, 2) }}</td></tr>
            @if($order->discount > 0)
            <tr><td colspan="3" style="text-align:right;padding-right:12px">Discount</td><td style="text-align:right;color:#e55">−₹{{ number_format($order->discount, 2) }}</td></tr>
            @endif
            <tr style="font-weight:600"><td colspan="3" style="text-align:right;padding-right:12px">Total</td><td style="text-align:right">₹{{ number_format($order->total, 2) }}</td></tr>
        </tfoot>
    </table>
</div>

<div style="margin-top:12px">
    <a href="{{ route('admin.mataji-orders.index') }}" class="btn-secondary">← Back to orders</a>
</div>

@endsection
