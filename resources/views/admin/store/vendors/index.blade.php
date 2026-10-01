@extends('layouts.app', ['title' => 'Vendors'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🏪 Vendors</span><small>Manage partner vendors selling on the platform</small></div>
    <div style="display:flex;gap:10px">
        @if($pending > 0)
            <span style="padding:9px 13px;border-radius:9px;background:#fff0d3;color:#a07020;font-weight:700;font-size:13px">{{ $pending }} pending approval</span>
        @endif
        <a class="nav-link topbar-link" href="{{ route('admin.store.vendors.create') }}" style="background:#f0ecff;color:#5c4bb7">+ Add Vendor</a>
    </div>
</header>

<div style="max-width:1100px;margin:30px auto;padding:0 20px 80px">
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

<form method="GET" style="display:flex;gap:10px;margin-bottom:18px">
    <input name="search" placeholder="Business name / email…" value="{{ request('search') }}"
        style="padding:9px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:13px;width:240px">
    <select name="status" style="padding:9px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:13px">
        <option value="">All Statuses</option>
        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
        <option value="active" @selected(request('status') === 'active')>Active</option>
        <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
    </select>
    <button type="submit" style="padding:9px 18px;background:#6246ea;color:#fff;border:none;border-radius:9px;font-weight:700;cursor:pointer;font-size:13px">Filter</button>
</form>

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;overflow:hidden">
<table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead>
        <tr style="background:#f9f9fb;border-bottom:1px solid #e6e8f0">
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Business</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Login Account</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Location</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Products</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Status</th>
            <th style="padding:12px 16px"></th>
        </tr>
    </thead>
    <tbody>
    @forelse($vendors as $v)
        <tr style="border-bottom:1px solid #f0f2f8">
            <td style="padding:13px 16px">
                <div style="display:flex;align-items:center;gap:10px">
                    @if($v->logo)
                        <img src="{{ asset('storage/'.$v->logo) }}" style="width:36px;height:36px;border-radius:9px;object-fit:cover">
                    @else
                        <div style="width:36px;height:36px;border-radius:9px;background:#f0ecff;display:grid;place-items:center;font-size:17px">🏪</div>
                    @endif
                    <div>
                        <b style="color:#15233d">{{ $v->business_name }}</b>
                        @if($v->gstin)<div style="font-size:11px;font-family:monospace;color:#8a9ab8">GSTIN: {{ $v->gstin }}</div>@endif
                    </div>
                </div>
            </td>
            <td style="padding:13px 16px;color:#536078">
                <div>{{ $v->user->name }}</div>
                <div style="font-size:12px;color:#8a9ab8">{{ $v->user->email }}</div>
            </td>
            <td style="padding:13px 16px;color:#536078">{{ implode(', ', array_filter([$v->city, $v->state])) ?: '—' }}</td>
            <td style="padding:13px 16px;text-align:center;font-weight:700;color:#15233d">{{ $v->products_count }}</td>
            <td style="padding:13px 16px">
                @php $sc = ['pending' => '#fff0d3:#a07020','active' => '#e4f6ea:#276946','suspended' => '#fdeaea:#c0392b']; $c = explode(':', $sc[$v->status]); @endphp
                <span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $c[0] }};color:{{ $c[1] }}">{{ ucfirst($v->status) }}</span>
            </td>
            <td style="padding:13px 16px;text-align:right;white-space:nowrap">
                <a href="{{ route('admin.store.vendors.show', $v) }}" style="color:#5c4bb7;font-weight:700;font-size:12px;margin-right:8px">View</a>
                @if($v->status === 'pending')
                    <form method="POST" action="{{ route('admin.store.vendors.approve', $v) }}" style="display:inline">
                        @csrf <button type="submit" style="border:none;background:none;color:#276946;font-weight:700;font-size:12px;cursor:pointer;margin-right:8px">Approve</button>
                    </form>
                @elseif($v->status === 'active')
                    <form method="POST" action="{{ route('admin.store.vendors.suspend', $v) }}" style="display:inline" onsubmit="return confirm('Suspend this vendor?')">
                        @csrf <button type="submit" style="border:none;background:none;color:#c0392b;font-weight:700;font-size:12px;cursor:pointer">Suspend</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.store.vendors.approve', $v) }}" style="display:inline">
                        @csrf <button type="submit" style="border:none;background:none;color:#276946;font-weight:700;font-size:12px;cursor:pointer">Re-activate</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6" style="padding:40px;text-align:center;color:#8a9ab8">No vendors yet.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div style="margin-top:16px">{{ $vendors->links() }}</div>
</div>
</main>
@endsection
