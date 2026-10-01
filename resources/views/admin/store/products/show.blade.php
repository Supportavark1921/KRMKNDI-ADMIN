@extends('layouts.app', ['title' => $product->name])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🛍 {{ $product->name }}</span><small>{{ $product->product_code }}</small></div>
    <div style="display:flex;gap:10px">
        <a class="nav-link topbar-link" href="{{ route('admin.store.products.edit', $product) }}" style="background:#f0ecff;color:#5c4bb7">Edit</a>
        <a class="nav-link topbar-link" href="{{ route('admin.store.inventory.history', $product) }}">Stock Log</a>
        <a class="nav-link topbar-link" href="{{ route('admin.store.products.index') }}">← Products</a>
    </div>
</header>

<div style="max-width:1100px;margin:30px auto;padding:0 20px 80px;display:grid;grid-template-columns:minmax(0,1.4fr) 320px;gap:20px;align-items:start">

{{-- Main --}}
<div>
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:28px;margin-bottom:18px">
    <div style="display:flex;gap:20px;margin-bottom:20px">
        @if($product->primaryImage)
            <img src="{{ $product->primaryImage->url() }}" style="width:100px;height:100px;border-radius:14px;object-fit:cover;flex-shrink:0">
        @endif
        <div>
            <h2 style="margin:0 0 6px;font-size:20px;color:#15233d">{{ $product->name }}</h2>
            <div style="font-size:13px;color:#8a9ab8;margin-bottom:8px">{{ $product->category?->name }} · {{ $product->product_code }}</div>
            @php $tc = ['NORMAL' => '#f3f5f9:#536078','MATAJI_OFFERING' => '#faf0df:#a07020','MATAJI_OFFERED_RESALE' => '#f0e8f8:#6a3a8f']; $c = explode(':', $tc[$product->product_type] ?? '#f3f5f9:#536078'); @endphp
            <span style="padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $c[0] }};color:{{ $c[1] }}">{{ str_replace('_', ' ', $product->product_type) }}</span>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
        <div><span style="font-size:11px;color:#8a9ab8;display:block">Price</span><b style="font-size:18px;color:#15233d">₹{{ number_format($product->price, 2) }}</b></div>
        @if($product->compare_at_price)
        <div><span style="font-size:11px;color:#8a9ab8;display:block">Compare At</span><b style="font-size:15px;text-decoration:line-through;color:#8a9ab8">₹{{ number_format($product->compare_at_price, 2) }}</b></div>
        @endif
        <div><span style="font-size:11px;color:#8a9ab8;display:block">SKU</span><b style="font-family:monospace;color:#15233d">{{ $product->sku ?: '—' }}</b></div>
        <div><span style="font-size:11px;color:#8a9ab8;display:block">Brand/Source</span><b style="color:#15233d">{{ $product->brand_source ?: '—' }}</b></div>
    </div>

    @if($product->description)
    <div style="border-top:1px solid #f0f2f8;padding-top:14px;font-size:13px;color:#536078;line-height:1.6">{{ $product->description }}</div>
    @endif
</div>

@if($product->attributes->count())
<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:22px;margin-bottom:18px">
    <h3 style="margin:0 0 14px;font-size:14px;font-weight:800;color:#15233d">Attributes</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
    @foreach($product->attributes as $attr)
        <div style="display:flex;gap:8px;font-size:13px"><span style="color:#8a9ab8;min-width:90px">{{ $attr->key }}</span><b style="color:#15233d">{{ $attr->value }}</b></div>
    @endforeach
    </div>
</div>
@endif

@if($product->images->count())
<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:22px;margin-bottom:18px">
    <h3 style="margin:0 0 14px;font-size:14px;font-weight:800;color:#15233d">Images</h3>
    <div style="display:flex;flex-wrap:wrap;gap:10px">
        @foreach($product->images as $img)
        <div style="position:relative">
            <img src="{{ $img->url() }}" style="width:78px;height:78px;border-radius:10px;object-fit:cover;border:{{ $img->is_primary ? '2px solid #6246ea' : '1px solid #dde1ef' }}">
            <form method="POST" action="{{ route('admin.store.products.images.delete', $img) }}" style="position:absolute;top:-5px;right:-5px" onsubmit="return confirm('Remove?')">
                @csrf @method('DELETE')
                <button type="submit" style="width:18px;height:18px;border-radius:50%;background:#c0392b;color:#fff;border:none;cursor:pointer;font-size:11px">×</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Recent inventory log --}}
@if($product->inventoryTransactions->count())
<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:22px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
        <h3 style="margin:0;font-size:14px;font-weight:800;color:#15233d">Recent Stock Movements</h3>
        <a href="{{ route('admin.store.inventory.history', $product) }}" style="font-size:12px;color:#5c4bb7;font-weight:700">Full log →</a>
    </div>
    @foreach($product->inventoryTransactions->take(5) as $tx)
    <div style="display:flex;justify-content:space-between;font-size:12px;padding:7px 0;border-bottom:1px solid #f0f2f8">
        <span style="color:#536078">{{ $tx->type }}</span>
        <span style="font-weight:700;color:{{ $tx->quantity_change >= 0 ? '#276946' : '#c0392b' }}">{{ $tx->quantity_change >= 0 ? '+' : '' }}{{ $tx->quantity_change }}</span>
        <span style="color:#8a9ab8">{{ $tx->created_at->diffForHumans() }}</span>
    </div>
    @endforeach
</div>
@endif
</div>

{{-- Sidebar --}}
<div>
<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:22px;margin-bottom:18px">
    <h3 style="margin:0 0 14px;font-size:14px;font-weight:800;color:#15233d">Inventory</h3>
    @php $inv = $product->inventory; @endphp
    @if($inv)
        @foreach(['available_stock' => ['Available','#276946','#e4f6ea'], 'reserved_stock' => ['Reserved','#a07020','#faf0df'], 'sold_stock' => ['Sold','#536078','#f3f5f9'], 'offering_stock' => ['At Temple','#6a3a8f','#f0e8f8'], 'total_stock' => ['Total','#15233d','#fff']] as $field => [$label, $color, $bg])
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f0f2f8;font-size:13px">
            <span style="color:#8a9ab8">{{ $label }}</span>
            <b style="color:{{ $color }}">{{ $inv->$field }}</b>
        </div>
        @endforeach
    @endif

    <form method="POST" action="{{ route('admin.store.inventory.add', $product) }}" style="margin-top:16px">
        @csrf
        <div style="font-size:11px;font-weight:700;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Add Stock</div>
        <div style="display:flex;gap:8px">
            <input name="quantity" type="number" min="1" placeholder="Qty" style="flex:1;padding:8px 10px;border:1px solid #dde1ef;border-radius:8px;font-size:13px">
            <button type="submit" style="padding:8px 14px;background:#276946;color:#fff;border:none;border-radius:8px;font-weight:700;font-size:12px;cursor:pointer">Add</button>
        </div>
    </form>
</div>

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:22px">
    <h3 style="margin:0 0 14px;font-size:14px;font-weight:800;color:#15233d">Mataji</h3>
    @if($product->mataji)
        <b style="color:#15233d">{{ $product->mataji->name }}</b>
        @if($product->mataji->temple_name)<div style="font-size:12px;color:#8a9ab8">{{ $product->mataji->temple_name }}</div>@endif
    @else
        <span style="color:#8a9ab8;font-size:13px">Available for all Matajis</span>
    @endif

    <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
        @if($product->offering_eligible)<span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;background:#e4f6ea;color:#276946">Offering Eligible</span>@endif
        @if($product->resale_eligible)<span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;background:#f0e8f8;color:#6a3a8f">Resale Eligible</span>@endif
    </div>
</div>
</div>

</div>
</main>
@endsection
