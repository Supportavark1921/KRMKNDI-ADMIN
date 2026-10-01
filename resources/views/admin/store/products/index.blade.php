@extends('layouts.app', ['title' => 'Products'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🛍 Products</span><small>Sarees, Chunaris and devotional items</small></div>
    <div style="display:flex;gap:10px">
        <a class="nav-link topbar-link" href="{{ route('admin.store.products.create') }}" style="background:#f0ecff;color:#5c4bb7">+ Add Product</a>
        <a class="nav-link topbar-link" href="{{ route('dashboard') }}">Dashboard</a>
    </div>
</header>

<div style="max-width:1200px;margin:30px auto;padding:0 20px 80px">
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="notice error">{{ session('error') }}</div>@endif

{{-- Filters --}}
<form method="GET" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
    <input name="search" placeholder="Search name / code / SKU…" value="{{ request('search') }}"
        style="padding:9px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:13px;width:220px">
    <select name="category" style="padding:9px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:13px">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="type" style="padding:9px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:13px">
        <option value="">All Types</option>
        <option value="NORMAL" @selected(request('type') === 'NORMAL')>Normal</option>
        <option value="MATAJI_OFFERING" @selected(request('type') === 'MATAJI_OFFERING')>Mataji Offering</option>
        <option value="MATAJI_OFFERED_RESALE" @selected(request('type') === 'MATAJI_OFFERED_RESALE')>Resale</option>
    </select>
    <select name="status" style="padding:9px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:13px">
        <option value="">All Statuses</option>
        <option value="active" @selected(request('status') === 'active')>Active</option>
        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        <option value="sold_out" @selected(request('status') === 'sold_out')>Sold Out</option>
    </select>
    <button type="submit" style="padding:9px 18px;background:#6246ea;color:#fff;border:none;border-radius:9px;font-weight:700;cursor:pointer;font-size:13px">Filter</button>
    @if(request()->hasAny(['search','category','type','status']))
        <a href="{{ route('admin.store.products.index') }}" style="padding:9px 14px;border:1px solid #dde1ef;border-radius:9px;font-size:13px;color:#536078">Clear</a>
    @endif
</form>

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;overflow:hidden">
<table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead>
        <tr style="background:#f9f9fb;border-bottom:1px solid #e6e8f0">
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Product</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Type</th>
            <th style="padding:12px 16px;text-align:right;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Price</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Stock</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Status</th>
            <th style="padding:12px 16px"></th>
        </tr>
    </thead>
    <tbody>
    @forelse($products as $p)
        @php $trashed = $p->trashed(); @endphp
        <tr style="border-bottom:1px solid #f0f2f8;{{ $trashed ? 'opacity:.5' : '' }}">
            <td style="padding:13px 16px">
                <div style="display:flex;align-items:center;gap:11px">
                    @php $img = $p->primaryImage; @endphp
                    @if($img)
                        <img src="{{ $img->url() }}" style="width:38px;height:38px;border-radius:9px;object-fit:cover">
                    @else
                        <div style="width:38px;height:38px;border-radius:9px;background:#f3f5f9;display:grid;place-items:center">🛍</div>
                    @endif
                    <div>
                        <b style="display:block;color:#15233d">{{ $p->name }}</b>
                        <small style="color:#8a9ab8;font-family:monospace">{{ $p->product_code }}</small>
                    </div>
                </div>
            </td>
            <td style="padding:13px 16px">
                @php $typeColors = ['NORMAL' => '#f3f5f9:#536078', 'MATAJI_OFFERING' => '#faf0df:#a07020', 'MATAJI_OFFERED_RESALE' => '#f0e8f8:#6a3a8f']; $tc = explode(':', $typeColors[$p->product_type] ?? '#f3f5f9:#536078'); @endphp
                <span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $tc[0] }};color:{{ $tc[1] }}">{{ str_replace('_', ' ', $p->product_type) }}</span>
            </td>
            <td style="padding:13px 16px;text-align:right;font-weight:700;color:#15233d">₹{{ number_format($p->price, 2) }}</td>
            <td style="padding:13px 16px;text-align:center">
                @if($p->inventory)
                    <span style="font-weight:700;color:{{ $p->inventory->available_stock > 0 ? '#276946' : '#c0392b' }}">{{ $p->inventory->available_stock }}</span>
                @else
                    <span style="color:#8a9ab8">—</span>
                @endif
            </td>
            <td style="padding:13px 16px">
                @php $sc = ['active' => '#e4f6ea:#276946','draft' => '#f3f5f9:#536078','inactive' => '#fdeaea:#c0392b','sold_out' => '#fff0d3:#a07020']; $c = explode(':', $sc[$p->status] ?? '#f3f5f9:#536078'); @endphp
                <span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $c[0] }};color:{{ $c[1] }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span>
                @if($trashed) <span style="margin-left:4px;padding:2px 6px;border-radius:20px;font-size:10px;background:#fdeaea;color:#c0392b;font-weight:700">Archived</span> @endif
            </td>
            <td style="padding:13px 16px;text-align:right;white-space:nowrap">
                @if($trashed)
                    <form method="POST" action="{{ route('admin.store.products.restore', $p->id) }}" style="display:inline">
                        @csrf <button type="submit" style="border:none;background:none;color:#276946;font-weight:700;font-size:12px;cursor:pointer">Restore</button>
                    </form>
                @else
                    <a href="{{ route('admin.store.products.show', $p) }}" style="color:#5c4bb7;font-weight:700;font-size:12px;margin-right:10px">View</a>
                    <a href="{{ route('admin.store.products.edit', $p) }}" style="color:#5c4bb7;font-weight:700;font-size:12px;margin-right:10px">Edit</a>
                    <form method="POST" action="{{ route('admin.store.products.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Archive this product?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="border:none;background:none;color:#c0392b;font-weight:700;font-size:12px;cursor:pointer">Archive</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6" style="padding:40px;text-align:center;color:#8a9ab8">No products found.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div style="margin-top:16px">{{ $products->links() }}</div>
</div>
</main>
@endsection
