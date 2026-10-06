@extends('layouts.app')
@section('title', 'Samagri Orders')
@section('content')

<div class="store-header">
    <div>
        <h1 class="store-title">Samagri Orders</h1>
        <p class="store-sub">Customer orders placed through the app shop.</p>
    </div>
</div>

<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, phone or order ID…" class="form-input" style="width:220px">
    <select name="status" class="form-input">
        <option value="">All statuses</option>
        @foreach($statuses as $s)
        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-secondary">Filter</button>
    <a href="{{ route('admin.samagri-orders.index') }}" class="btn-secondary">Clear</a>
</form>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

<div class="store-card">
    <table class="store-table">
        <thead><tr>
            <th>#</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Items</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th></th>
        </tr></thead>
        <tbody>
        @forelse($orders as $o)
        <tr class="{{ $o->trashed() ? 'opacity-50' : '' }}">
            <td>{{ $o->id }}</td>
            <td>{{ $o->name }}</td>
            <td>{{ $o->phone }}</td>
            <td style="text-align:center">{{ $o->items_count ?? '—' }}</td>
            <td>₹{{ number_format($o->total_amount, 2) }}</td>
            <td>
                @php $color = match($o->status) {
                    'confirmed','processing','shipped' => 'badge-pending',
                    'delivered' => 'badge-active',
                    'cancelled' => 'badge-inactive',
                    default => 'badge-pending',
                }; @endphp
                <span class="badge {{ $color }}">{{ ucfirst($o->status) }}</span>
            </td>
            <td style="font-size:12px">{{ $o->created_at->format('d M Y') }}</td>
            <td style="text-align:right;white-space:nowrap">
                <a href="{{ route('admin.samagri-orders.show', $o) }}" class="btn-sm">View</a>
                @if($o->trashed())
                    @can('samagri-orders.restore')
                    <form method="POST" action="{{ route('admin.samagri-orders.restore', $o->id) }}" style="display:inline">
                        @csrf <button class="btn-sm">Restore</button>
                    </form>
                    @endcan
                @else
                    @can('samagri-orders.delete')
                    <form method="POST" action="{{ route('admin.samagri-orders.destroy', $o) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn-sm btn-danger" onclick="return confirm('Archive order #{{ $o->id }}?')">Archive</button>
                    </form>
                    @endcan
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:24px;color:#888">No orders found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="padding:12px">{{ $orders->links() }}</div>
</div>
@endsection
