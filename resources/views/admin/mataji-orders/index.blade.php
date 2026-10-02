@extends('layouts.app')
@section('title', 'Mataji Orders')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">Mataji Orders</h1>
        <p class="store-sub">Saree sales and purchases managed by Guruji.</p>
    </div>
    @can('mataji-orders.create')
    <a href="{{ route('admin.mataji-orders.create') }}" class="btn-primary">+ New Order</a>
    @endcan
</div>

<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Customer name/phone…" class="form-input" style="width:200px">
    <select name="type" class="form-input">
        <option value="">All types</option>
        @foreach($types as $t)
        <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
        @endforeach
    </select>
    <select name="status" class="form-input">
        <option value="">All status</option>
        @foreach($statuses as $s)
        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-secondary">Filter</button>
    <a href="{{ route('admin.mataji-orders.index') }}" class="btn-secondary">Clear</a>
</form>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

<div class="store-card">
    <table class="store-table">
        <thead><tr>
            <th>#</th><th>Guruji</th><th>Mataji</th><th>Customer</th><th>Type</th><th>Status</th><th>Total</th><th>Date</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($orders as $o)
        <tr class="{{ $o->trashed() ? 'opacity-50' : '' }}">
            <td>{{ $o->id }}</td>
            <td>{{ $o->guruji?->name ?? '—' }}</td>
            <td>{{ $o->mataji?->name ?? '—' }}</td>
            <td>{{ $o->customerDisplayName() }}</td>
            <td><span class="badge badge-active">{{ ucfirst($o->type) }}</span></td>
            <td>
                @php $color = match($o->status) {
                    'confirmed','paid','delivered' => 'badge-active',
                    'cancelled' => 'badge-inactive',
                    default => 'badge-pending',
                }; @endphp
                <span class="badge {{ $color }}">{{ ucfirst($o->status) }}</span>
            </td>
            <td>₹{{ number_format($o->total, 2) }}</td>
            <td style="font-size:12px">{{ $o->created_at->format('d M Y') }}</td>
            <td style="text-align:right;white-space:nowrap">
                <a href="{{ route('admin.mataji-orders.show', $o) }}" class="btn-sm">View</a>
                @if($o->isDraft())
                @can('mataji-orders.update')
                <a href="{{ route('admin.mataji-orders.edit', $o) }}" class="btn-sm">Edit</a>
                <form method="POST" action="{{ route('admin.mataji-orders.confirm', $o) }}" style="display:inline">
                    @csrf <button class="btn-sm btn-success" onclick="return confirm('Confirm and update stock?')">Confirm</button>
                </form>
                @endcan
                @endif
                @if(!$o->isCancelled() && !$o->trashed() && $o->status !== 'delivered')
                @can('mataji-orders.update')
                <form method="POST" action="{{ route('admin.mataji-orders.cancel', $o) }}" style="display:inline">
                    @csrf <button class="btn-sm btn-danger" onclick="return confirm('Cancel this order?')">Cancel</button>
                </form>
                @endcan
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:24px;color:#888">No orders found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="padding:12px">{{ $orders->links() }}</div>
</div>
@endsection
