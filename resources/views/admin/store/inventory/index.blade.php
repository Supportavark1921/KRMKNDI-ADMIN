@extends('layouts.app', ['title' => 'Inventory'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>📦 Inventory</span><small>Stock levels across all products</small></div>
    <a class="nav-link topbar-link" href="{{ route('dashboard') }}">Dashboard</a>
</header>

<div style="max-width:1100px;margin:30px auto;padding:0 20px 80px">
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;overflow:hidden">
<table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead>
        <tr style="background:#f9f9fb;border-bottom:1px solid #e6e8f0">
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Product</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Available</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Reserved</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Sold</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Total</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Add Stock</th>
        </tr>
    </thead>
    <tbody>
    @forelse($products as $p)
        <tr style="border-bottom:1px solid #f0f2f8">
            <td style="padding:13px 16px">
                <a href="{{ route('admin.store.products.show', $p) }}" style="color:#15233d;font-weight:700;text-decoration:none">{{ $p->name }}</a>
                <div style="font-size:11px;font-family:monospace;color:#8a9ab8">{{ $p->product_code }}</div>
            </td>
            @php $inv = $p->inventory; @endphp
            <td style="padding:13px 16px;text-align:center;font-weight:700;color:{{ ($inv->available_stock ?? 0) > 0 ? '#276946' : '#c0392b' }}">{{ $inv->available_stock ?? 0 }}</td>
            <td style="padding:13px 16px;text-align:center;color:#a07020;font-weight:600">{{ $inv->reserved_stock ?? 0 }}</td>
            <td style="padding:13px 16px;text-align:center;color:#536078">{{ $inv->sold_stock ?? 0 }}</td>
            <td style="padding:13px 16px;text-align:center;font-weight:700;color:#15233d">{{ $inv->total_stock ?? 0 }}</td>
            <td style="padding:13px 16px">
                <form method="POST" action="{{ route('admin.store.inventory.add', $p) }}" style="display:flex;gap:6px;justify-content:center">
                    @csrf
                    <input name="quantity" type="number" min="1" placeholder="Qty" style="width:70px;padding:6px 8px;border:1px solid #dde1ef;border-radius:7px;font-size:12px">
                    <button type="submit" style="padding:6px 12px;background:#276946;color:#fff;border:none;border-radius:7px;font-weight:700;font-size:12px;cursor:pointer">Add</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" style="padding:40px;text-align:center;color:#8a9ab8">No active products.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div style="margin-top:16px">{{ $products->links() }}</div>
</div>
</main>
@endsection
