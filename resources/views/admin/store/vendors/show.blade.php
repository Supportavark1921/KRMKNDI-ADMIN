@extends('layouts.app', ['title' => $vendor->business_name])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🏪 {{ $vendor->business_name }}</span><small>Vendor profile</small></div>
    <div style="display:flex;gap:10px">
        <a class="nav-link topbar-link" href="{{ route('admin.store.vendors.edit', $vendor) }}" style="background:#f0ecff;color:#5c4bb7">Edit</a>
        @if($vendor->status === 'pending')
            <form method="POST" action="{{ route('admin.store.vendors.approve', $vendor) }}">
                @csrf <button type="submit" style="padding:9px 14px;background:#276946;color:#fff;border:none;border-radius:9px;font-weight:700;cursor:pointer;font-size:13px">✓ Approve</button>
            </form>
        @elseif($vendor->status === 'active')
            <form method="POST" action="{{ route('admin.store.vendors.suspend', $vendor) }}" onsubmit="return confirm('Suspend this vendor?')">
                @csrf <button type="submit" style="padding:9px 14px;background:#c0392b;color:#fff;border:none;border-radius:9px;font-weight:700;cursor:pointer;font-size:13px">Suspend</button>
            </form>
        @endif
        <a class="nav-link topbar-link" href="{{ route('admin.store.vendors.index') }}">← All Vendors</a>
    </div>
</header>

<div style="max-width:1000px;margin:30px auto;padding:0 20px 80px;display:grid;grid-template-columns:minmax(0,1.3fr) 280px;gap:20px;align-items:start">

<div>
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:26px;margin-bottom:18px">
    <div style="display:flex;gap:16px;align-items:flex-start">
        @if($vendor->logo)
            <img src="{{ asset('storage/'.$vendor->logo) }}" style="width:72px;height:72px;border-radius:14px;object-fit:cover;flex-shrink:0">
        @else
            <div style="width:72px;height:72px;border-radius:14px;background:#f0ecff;display:grid;place-items:center;font-size:32px;flex-shrink:0">🏪</div>
        @endif
        <div>
            <h2 style="margin:0 0 4px;font-size:20px;color:#15233d">{{ $vendor->business_name }}</h2>
            <div style="font-size:13px;color:#8a9ab8;margin-bottom:8px">{{ $vendor->user->name }} · {{ $vendor->user->email }}</div>
            @php $sc = ['pending' => '#fff0d3:#a07020','active' => '#e4f6ea:#276946','suspended' => '#fdeaea:#c0392b']; $c = explode(':', $sc[$vendor->status]); @endphp
            <span style="padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $c[0] }};color:{{ $c[1] }}">{{ ucfirst($vendor->status) }}</span>
        </div>
    </div>
</div>

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:24px;margin-bottom:18px">
    <h3 style="margin:0 0 16px;font-size:14px;font-weight:800;color:#15233d">Details</h3>
    @foreach([
        'Contact Person' => $vendor->contact_person,
        'Phone'          => $vendor->phone,
        'Email'          => $vendor->email,
        'City/State'     => implode(', ', array_filter([$vendor->city, $vendor->state])),
        'GSTIN'          => $vendor->gstin,
        'PAN'            => $vendor->pan,
        'Approved By'    => $vendor->approvedBy?->name,
        'Approved At'    => $vendor->approved_at?->format('d M Y'),
    ] as $label => $value)
    @if($value)
    <div style="display:flex;gap:12px;padding:7px 0;border-bottom:1px solid #f0f2f8;font-size:13px">
        <span style="color:#8a9ab8;min-width:110px">{{ $label }}</span>
        <b style="color:#15233d">{{ $value }}</b>
    </div>
    @endif
    @endforeach
</div>

@if($vendor->bank_details)
<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:24px;margin-bottom:18px">
    <h3 style="margin:0 0 16px;font-size:14px;font-weight:800;color:#15233d">Bank Details</h3>
    @foreach(['bank_name' => 'Bank', 'account_no' => 'Account No', 'ifsc' => 'IFSC'] as $key => $label)
        @if($vendor->bank_details[$key] ?? false)
        <div style="display:flex;gap:12px;padding:7px 0;border-bottom:1px solid #f0f2f8;font-size:13px">
            <span style="color:#8a9ab8;min-width:110px">{{ $label }}</span>
            <b style="color:#15233d;font-family:monospace">{{ $vendor->bank_details[$key] }}</b>
        </div>
        @endif
    @endforeach
</div>
@endif

@if($vendor->admin_notes)
<div style="background:#fffbf0;border:1px solid #f6e5bd;border-radius:18px;padding:24px;margin-bottom:18px">
    <h3 style="margin:0 0 8px;font-size:14px;font-weight:800;color:#a07020">Admin Notes</h3>
    <p style="margin:0;font-size:13px;color:#735f44;line-height:1.6">{{ $vendor->admin_notes }}</p>
</div>
@endif
</div>

<div>
<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:22px">
    <h3 style="margin:0 0 14px;font-size:14px;font-weight:800;color:#15233d">Products ({{ $vendor->products->count() }})</h3>
    @forelse($vendor->products as $p)
    <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f0f2f8;font-size:12px">
        <a href="{{ route('admin.store.products.show', $p) }}" style="color:#5c4bb7;font-weight:600">{{ $p->name }}</a>
        <span style="color:#8a9ab8">₹{{ number_format($p->price, 0) }}</span>
    </div>
    @empty
        <p style="color:#8a9ab8;font-size:13px">No products yet.</p>
    @endforelse
    @if($vendor->products->count() >= 10)
        <a href="{{ route('admin.store.products.index', ['vendor_id' => $vendor->id]) }}" style="display:block;margin-top:10px;font-size:12px;color:#5c4bb7;font-weight:700">View all →</a>
    @endif
</div>
</div>

</div>
</main>
@endsection
