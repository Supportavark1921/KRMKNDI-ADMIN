@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<style>
.don-page{background:radial-gradient(circle at 88% 5%,#f0fff4 0,transparent 22%),#f4f7f4!important}
.don-detail-wrap{max-width:900px;margin:0 auto}
.don-detail-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin:32px auto 20px;padding:30px 36px;border-radius:20px;color:#fff;background:radial-gradient(circle at 82% 10%,#40c080 0,transparent 28%),linear-gradient(118deg,#062010,#1a6040)}
.don-detail-hero h1{margin:6px 0;font-size:26px;color:#fff}
.don-detail-hero p{margin:0;color:#c0f0d8;font-size:13px}
.hero-overline{color:#80ffc0;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.detail-panel{padding:22px 26px;border:1px solid #d0e8d8;border-radius:16px;background:#fff;box-shadow:0 4px 12px #1a604006}
.panel-title{font-size:11px;font-weight:800;color:#507060;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px;padding-bottom:11px;border-bottom:1px solid #e8f5ec}
.detail-row{margin-bottom:14px}
.detail-row:last-child{margin-bottom:0}
.d-label{font-size:11px;font-weight:700;color:#608070;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px}
.d-value{font-size:15px;font-weight:700;color:#102010}
.d-value.muted{font-weight:400;color:#405040;font-size:14px}
.breakdown-panel{padding:22px 26px;border:1px solid #d0e8d8;border-radius:16px;background:#fff;margin-bottom:16px}
.breakdown-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #e8f5ec;font-size:14px}
.breakdown-row:last-child{border-bottom:0}
.breakdown-row .bl{color:#405040}
.breakdown-row .br{font-weight:700;color:#102010}
.breakdown-total{display:flex;justify-content:space-between;align-items:center;padding:14px 0 0;margin-top:4px;border-top:2px solid #c0e0c8;font-size:16px;font-weight:900}
.breakdown-total .bl{color:#102010}
.breakdown-total .br{color:#1a8040;font-size:20px}
.order-id-big{font-family:ui-monospace,monospace;font-size:24px;font-weight:900;color:#80ffc0;letter-spacing:.04em}
.st-confirmed{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:13px;font-weight:750}
.st-pending{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:13px;font-weight:750}
.st-processing{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1a4a80;background:#e3eeff;font-size:13px;font-weight:750}
.st-shipped{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#5a1a80;background:#f0e3ff;font-size:13px;font-weight:750}
.st-delivered{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:13px;font-weight:750}
.st-cancelled{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#606070;background:#f0f0f5;font-size:13px;font-weight:750}
.st-paid{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:13px;font-weight:750}
.st-unpaid{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:13px;font-weight:750}
.st-screenshot_uploaded{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1a4a80;background:#e3eeff;font-size:13px;font-weight:750}
.st-confirmed::before,.st-pending::before,.st-processing::before,.st-shipped::before,.st-delivered::before,.st-cancelled::before,.st-paid::before,.st-unpaid::before,.st-screenshot_uploaded::before{content:"";width:7px;height:7px;border-radius:50%}
.st-confirmed::before{background:#28a76a}.st-pending::before{background:#e69c3a}.st-processing::before{background:#1a6abf}.st-shipped::before{background:#8a3ab0}.st-delivered::before{background:#28a76a}.st-cancelled::before{background:#909090}.st-paid::before{background:#28a76a}.st-unpaid::before{background:#e69c3a}.st-screenshot_uploaded::before{background:#1a6abf}
.order-items-table{width:100%;border-collapse:collapse;font-size:14px}
.order-items-table th{text-align:left;padding:8px 0;color:#508060;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid #d0e8d8}
.order-items-table td{padding:10px 0;border-bottom:1px solid #e8f5ec;color:#2a3a2a}
.order-items-table tfoot td{border-bottom:0;border-top:2px solid #c0e0c8;padding-top:12px;font-weight:900;font-size:15px}
.btn-back{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:10px;border:1px solid #d0e8d8;color:#405040;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.btn-back:hover{background:#f0f8f2}
@media(max-width:650px){.detail-grid{grid-template-columns:1fr}}
</style>

<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span><a href="{{ route('admin.samagri-orders.index') }}" style="color:inherit;text-decoration:none">Samagri Orders</a> › Detail</span><small>Order #{{ $order->id }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="don-detail-wrap">
        @if(session('success'))<div style="margin:0 0 16px;padding:12px 18px;border-radius:10px;background:#d3f9d8;color:#1a4d2a;font-size:14px;font-weight:600">✓ {{ session('success') }}</div>@endif
        @if(session('error'))<div style="margin:0 0 16px;padding:12px 18px;border-radius:10px;background:#fdeaea;color:#8b3030;font-size:14px;font-weight:600">{{ session('error') }}</div>@endif

        <div class="don-detail-hero">
            <div>
                <span class="hero-overline">Samagri Order</span>
                <h1 class="order-id-big">Order #{{ $order->id }}</h1>
                <p>{{ $order->created_at->format('d M Y, g:i A') }} · {{ $order->name }}</p>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px">
                <span class="st-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                <div style="display:flex;gap:8px">
                    @if(!$order->trashed())
                        @can('samagri-orders.delete')
                        <form method="POST" action="{{ route('admin.samagri-orders.destroy', $order) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Archive this order?')" style="padding:6px 12px;border:1px solid rgba(255,255,255,.3);border-radius:8px;background:transparent;color:rgba(255,255,255,.8);font:600 12px inherit;cursor:pointer">Archive</button>
                        </form>
                        @endcan
                    @else
                        @can('samagri-orders.restore')
                        <form method="POST" action="{{ route('admin.samagri-orders.restore', $order->id) }}" style="display:inline">
                            @csrf <button style="padding:6px 12px;border:1px solid rgba(255,255,255,.3);border-radius:8px;background:transparent;color:rgba(255,255,255,.8);font:600 12px inherit;cursor:pointer">Restore</button>
                        </form>
                        @endcan
                    @endif
                </div>
            </div>
        </div>

        {{-- Order items (full width) --}}
        <div class="breakdown-panel">
            <p class="panel-title" style="margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #e8f5ec">Order Items</p>
            <table class="order-items-table">
                <thead><tr>
                    <th>Product</th>
                    <th>Unit</th>
                    <th style="text-align:center">Qty</th>
                    <th style="text-align:right">Unit Price</th>
                    <th style="text-align:right">Subtotal</th>
                </tr></thead>
                <tbody>
                @forelse($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td style="color:#608070">{{ $item->unit ?? '—' }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">₹{{ number_format($item->price, 2) }}</td>
                    <td style="text-align:right">₹{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:#888;padding:16px">No items.</td></tr>
                @endforelse
                </tbody>
                <tfoot>
                    @if($order->subtotal != $order->total_amount)
                    <tr><td colspan="4" style="text-align:right;padding-right:12px;color:#608070;font-size:13px">Subtotal</td><td style="text-align:right;font-size:14px;font-weight:700">₹{{ number_format($order->subtotal, 2) }}</td></tr>
                    @endif
                </tfoot>
            </table>
            <div class="breakdown-total"><span class="bl">Total</span><span class="br">₹{{ number_format($order->total_amount, 2) }}</span></div>
        </div>

        <div class="detail-grid">
            {{-- Customer --}}
            <div class="detail-panel">
                <p class="panel-title">Customer Information</p>
                <div class="detail-row"><div class="d-label">Name</div><div class="d-value">{{ $order->name }}</div></div>
                <div class="detail-row"><div class="d-label">Phone</div><div class="d-value muted">{{ $order->phone }}</div></div>
                @if($order->address)
                <div class="detail-row"><div class="d-label">Delivery Address</div><div class="d-value muted" style="white-space:pre-line">{{ $order->address }}</div></div>
                @endif
                @if($order->user)
                <div class="detail-row"><div class="d-label">Account</div><div class="d-value muted">USER-{{ str_pad($order->user->id, 4, '0', STR_PAD_LEFT) }}</div></div>
                @endif
                @if($order->notes)
                <div class="detail-row"><div class="d-label">Notes</div><div class="d-value muted">{{ $order->notes }}</div></div>
                @endif
            </div>

            {{-- Order Status --}}
            <div class="detail-panel">
                <p class="panel-title">Order Status</p>
                <div class="detail-row"><div class="d-label">Current Status</div><div><span class="st-{{ $order->status }}">{{ ucfirst($order->status) }}</span></div></div>
                <div class="detail-row"><div class="d-label">Order Total</div><div class="d-value">₹{{ number_format($order->total_amount, 2) }}</div></div>
                <div class="detail-row"><div class="d-label">Placed On</div><div class="d-value muted">{{ $order->created_at->format('d M Y, g:i A') }}</div></div>
            </div>

            {{-- Payment Details --}}
            <div class="detail-panel">
                <p class="panel-title">Payment Details</p>
                @php $ps = $order->payment_status ?? 'unpaid'; @endphp
                <div class="detail-row"><div class="d-label">Payment Status</div><div><span class="st-{{ $ps }}">{{ ucfirst(str_replace('_',' ',$ps)) }}</span></div></div>
                <div class="detail-row"><div class="d-label">Transaction ID</div><div class="d-value muted" style="font-family:ui-monospace,monospace;font-size:13px">{{ $order->payment_id ?: '—' }}</div></div>
                @if($order->payment_screenshot)
                <div class="detail-row">
                    <div class="d-label">Screenshot</div>
                    <a href="{{ Storage::disk('public')->url($order->payment_screenshot) }}" target="_blank">
                        <img src="{{ Storage::disk('public')->url($order->payment_screenshot) }}" style="max-height:160px;border-radius:6px;border:1px solid #c8e0d0;margin-top:4px;cursor:zoom-in">
                    </a>
                </div>
                @endif
            </div>

            {{-- Timestamps --}}
            <div class="detail-panel">
                <p class="panel-title">Timestamps</p>
                <div class="detail-row"><div class="d-label">Order Placed</div><div class="d-value">{{ $order->created_at->format('d M Y, g:i A') }}</div></div>
                <div class="detail-row"><div class="d-label">Last Updated</div><div class="d-value muted">{{ $order->updated_at->format('d M Y, g:i A') }}</div></div>
                <div class="detail-row"><div class="d-label">Internal ID</div><div class="d-value muted" style="font-family:ui-monospace,monospace">#{{ $order->id }}</div></div>
            </div>

            {{-- Update Booking Status --}}
            @can('samagri-orders.update')
            @if(!in_array($order->status, ['cancelled','delivered']) && !$order->trashed())
            <div class="detail-panel" style="grid-column:1/-1">
                <p class="panel-title">Update Booking Status</p>
                <form method="POST" action="{{ route('admin.samagri-orders.status', $order) }}">
                    @csrf
                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                        <div style="flex:1;min-width:200px">
                            <div class="d-label" style="margin-bottom:4px">Booking Status</div>
                            <select name="status" style="height:38px;width:100%;padding:0 10px;border:1px solid #c0d8c8;border-radius:8px;font:inherit">
                                @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                                <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" onclick="return confirm('Update order status?')" style="height:38px;padding:0 18px;border:0;border-radius:8px;background:#1a8040;color:#fff;font:700 14px inherit;cursor:pointer;white-space:nowrap">Save Booking Status</button>
                    </div>
                </form>
            </div>
            @endif
            @endcan

            {{-- Update Payment Status --}}
            @can('samagri-orders.update')
            <div class="detail-panel" style="grid-column:1/-1">
                <p class="panel-title">Update Payment Status</p>
                <form method="POST" action="{{ route('admin.samagri-orders.payment', $order) }}">
                    @csrf @method('PATCH')
                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                        <div style="flex:1;min-width:160px">
                            <div class="d-label" style="margin-bottom:4px">Payment Status</div>
                            <select name="payment_status" style="height:38px;width:100%;padding:0 10px;border:1px solid #c0d8c8;border-radius:8px;font:inherit">
                                @foreach(['unpaid','screenshot_uploaded','paid','cancelled'] as $ps)
                                <option value="{{ $ps }}" @selected(($order->payment_status ?? 'unpaid') === $ps)>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:2;min-width:200px">
                            <div class="d-label" style="margin-bottom:4px">Transaction ID</div>
                            <input type="text" name="payment_id" style="height:38px;width:100%;padding:0 10px;border:1px solid #c0d8c8;border-radius:8px;font:inherit" placeholder="Enter UTR / transaction ID" value="{{ old('payment_id', $order->payment_id) }}">
                        </div>
                        <button type="submit" style="height:38px;padding:0 18px;border:0;border-radius:8px;background:#1a8040;color:#fff;font:700 14px inherit;cursor:pointer;white-space:nowrap">Save Payment Status</button>
                    </div>
                </form>
            </div>
            @endcan
        </div>

        <div style="margin-top:18px;display:flex;gap:10px">
            <a href="{{ route('admin.samagri-orders.index') }}" class="btn-back">← All Orders</a>
        </div>
    </div>
</div>
@endsection
