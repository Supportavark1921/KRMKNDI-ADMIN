@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.ms-page{background:radial-gradient(circle at 90% 4%,#fff4e0 0,transparent 22%),#f8f7f2!important}
.ms-wrap{max-width:1100px;margin:0 auto;padding-bottom:40px}
.ms-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin:35px auto 22px;padding:32px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#f0b060 0,transparent 26%),linear-gradient(118deg,#1a0f02,#6b3a08)}
.ms-hero h1{margin:8px 0;font-size:30px;color:#fff}.ms-hero p{margin:0;color:#f8e8cc;font-size:14px}
.hero-overline{color:#ffe085;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.ms-add-btn{display:flex;align-items:center;gap:8px;padding:12px 16px;border-radius:12px;color:#3a1f02;background:linear-gradient(120deg,#ffdd7c,#f0a030);font:800 13px inherit;text-decoration:none;white-space:nowrap}
.ms-filters{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap}
.ms-filter-input{padding:9px 13px;border:1px solid #ede8e0;border-radius:10px;font:inherit;font-size:13px;color:#2a1810;background:#fff;outline:none}
.ms-filter-input:focus{border-color:#e8813a}
.ms-table-wrap{background:#fff;border:1px solid #ede8e0;border-radius:16px;overflow:hidden;box-shadow:0 4px 12px #6b3a0806}
table.ms-table{width:100%;border-collapse:collapse}
.ms-table th{background:#faf7f2;padding:12px 16px;text-align:left;font-size:11px;font-weight:800;color:#7a6050;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #ede8e0}
.ms-table td{padding:13px 16px;border-bottom:1px solid #f5f0e8;font-size:13px;color:#2a1810;vertical-align:middle}
.ms-table tr:last-child td{border-bottom:0}
.ms-table tr:hover td{background:#fffaf5}
.status-active{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.status-inactive{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:11px;font-weight:750}
.status-draft{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#445;background:#eef;font-size:11px;font-weight:750}
.status-sold_out{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#7a1818;background:#ffe0e0;font-size:11px;font-weight:750}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;border:1px solid #ede8e0;background:#fff;color:#7a6050;font-size:13px;cursor:pointer;text-decoration:none;transition:.15s}
.btn-icon:hover{border-color:#e8813a;color:#e8813a;background:#fff4eb}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.prod-img{width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid #ede8e0}
.flash-success{display:flex;align-items:center;gap:10px;margin:0 0 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.pag-wrap{margin-top:16px;display:flex;justify-content:center}
.pag-wrap .pagination{display:flex;gap:6px;list-style:none;margin:0;padding:0}
.pag-wrap .page-item .page-link{display:flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;border:1px solid #ede8e0;border-radius:9px;color:#7a6050;font-size:13px;font-weight:600;text-decoration:none;transition:.15s}
.pag-wrap .page-item.active .page-link{background:#e8813a;border-color:#e8813a;color:#fff}
</style>
<div class="dashboard ms-page">
    <div class="topbar">
        <div class="page-heading"><span>My Store › Products</span><small>Manage your products</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    <div class="ms-wrap">
        @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

        <div class="ms-hero">
            <div>
                <span class="hero-overline">My Store · {{ $guru->name }}</span>
                <h1>🛍 Products</h1>
                <p>Add and manage products visible to customers in the app.</p>
            </div>
            @can('my-store.create')
            <a href="{{ route('my.store.products.create') }}" class="ms-add-btn">＋ Add Product</a>
            @endcan
        </div>

        <form method="GET" class="ms-filters">
            <input type="text" name="search" class="ms-filter-input" placeholder="Search name…" value="{{ request('search') }}" style="flex:1;min-width:200px">
            <select name="status" class="ms-filter-input">
                <option value="">All Statuses</option>
                @foreach(['active','inactive','draft','sold_out'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>
            <button type="submit" style="padding:9px 16px;border-radius:10px;border:1px solid #ede8e0;background:#fff;font:600 13px inherit;cursor:pointer;color:#4a3020">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('my.store.products.index') }}" style="padding:9px 14px;border-radius:10px;border:1px solid #fdc9c9;background:#fff0f0;color:#b33;font:600 13px inherit;text-decoration:none">Clear</a>
            @endif
        </form>

        @if($products->isEmpty())
            <div style="padding:60px;text-align:center;border:1px dashed #e8c8a8;border-radius:16px;background:#fff">
                <div style="font-size:36px;margin-bottom:12px">🛍</div>
                <h3 style="margin:0 0 8px;color:#2a1810">No products yet</h3>
                <p style="color:#9a8070;margin:0 0 20px;font-size:14px">Create a category first, then add products.</p>
                <a href="{{ route('my.store.products.create') }}" style="padding:11px 18px;border-radius:10px;background:#e8813a;color:#fff;font:700 14px inherit;text-decoration:none">＋ Add Product</a>
            </div>
        @else
            <div class="ms-table-wrap">
                <table class="ms-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                @php $img = $product->images->first(); @endphp
                                @if($img)
                                    <img src="{{ Storage::disk('public')->url($img->path) }}" class="prod-img" alt="">
                                @else
                                    <div style="width:40px;height:40px;border-radius:8px;background:#fff4eb;display:flex;align-items:center;justify-content:center;font-size:18px">🛍</div>
                                @endif
                            </td>
                            <td style="font-weight:700">{{ $product->name }}<br><span style="font-weight:400;font-size:11px;color:#9a8070">{{ $product->sku }}</span></td>
                            <td style="color:#7a6050">{{ $product->category?->name ?? '—' }}</td>
                            <td style="font-weight:700">₹{{ number_format($product->price, 2) }}
                                @if($product->compare_at_price)
                                    <br><small style="text-decoration:line-through;color:#9a8070;font-weight:400">₹{{ number_format($product->compare_at_price, 2) }}</small>
                                @endif
                            </td>
                            <td><span class="status-{{ $product->status }}">{{ ucfirst(str_replace('_',' ',$product->status)) }}</span></td>
                            <td>
                                <div style="display:flex;gap:6px">
                                    <a href="{{ route('my.store.products.edit', $product) }}" class="btn-icon" title="Edit">✎</a>
                                    <form method="POST" action="{{ route('my.store.products.destroy', $product) }}" onsubmit="return confirm('Delete {{ $product->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Delete">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div class="pag-wrap">{{ $products->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
