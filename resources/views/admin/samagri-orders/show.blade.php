@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')

<div class="store-header">
    <div>
        <h1 class="store-title">Order #{{ $order->id }}</h1>
        <p class="store-sub">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        @can('samagri-orders.update')
        @if(!in_array($order->status, ['cancelled','delivered']) && !$order->trashed())
        <form method="POST" action="{{ route('admin.samagri-orders.status', $order) }}" style="display:flex;gap:6px;align-items:center">
            @csrf
            <select name="status" class="form-input" style="height:36px;padding:0 10px">
                @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn-primary" style="height:36px" onclick="return confirm('Update order status?')">Update</button>
        </form>
        @endif
        @endcan

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
    <div class="store-card">
        <h3 style="margin:0 0 12px">Order Info</h3>
        <dl style="display:grid;grid-template-columns:120px 1fr;gap:6px 12px;margin:0">
            <dt style="color:#888">Order ID</dt><dd>#{{ $order->id }}</dd>
            <dt style="color:#888">Status</dt>
            <dd>
                @php $color = match($order->status) {
                    'confirmed','processing','shipped' => 'badge-pending',
                    'delivered' => 'badge-active',
                    'cancelled' => 'badge-inactive',
                    default => 'badge-pending',
                }; @endphp
                <span class="badge {{ $color }}">{{ ucfirst($order->status) }}</span>
            </dd>
            <dt style="color:#888">Subtotal</dt><dd>₹{{ number_format($order->subtotal, 2) }}</dd>
            <dt style="color:#888">Total</dt><dd style="font-weight:600">₹{{ number_format($order->total_amount, 2) }}</dd>
            @if($order->notes)
            <dt style="color:#888">Notes</dt><dd>{{ $order->notes }}</dd>
            @endif
        </dl>
    </div>

    <div class="store-card">
        <h3 style="margin:0 0 12px">Customer</h3>
        <dl style="display:grid;grid-template-columns:120px 1fr;gap:6px 12px;margin:0">
            <dt style="color:#888">Name</dt><dd>{{ $order->name }}</dd>
            <dt style="color:#888">Phone</dt><dd>{{ $order->phone }}</dd>
            @if($order->address)
            <dt style="color:#888">Address</dt><dd style="white-space:pre-line">{{ $order->address }}</dd>
            @endif
            @if($order->user)
            <dt style="color:#888">Account</dt><dd>User #{{ $order->user->id }}</dd>
            @endif
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
