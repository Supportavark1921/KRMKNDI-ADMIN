@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.sync') }}">⟳ Sync Data</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>

    <div style="max-width:1200px;margin:32px auto 0">
        <div class="section-heading" style="margin-bottom:16px">
            <div><span style="font-size:22px;font-weight:800">PIN Codes</span></div>
        </div>

        <form method="get" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
            <select name="state_id" onchange="this.form.submit()" style="padding:9px 12px;border:1px solid #e6e9f0;border-radius:9px;font:inherit;min-width:180px">
                <option value="">All States</option>
                @foreach($states as $s)
                    <option value="{{ $s->id }}" @selected(request('state_id')==$s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
            <select name="district_id" onchange="this.form.submit()" style="padding:9px 12px;border:1px solid #e6e9f0;border-radius:9px;font:inherit;min-width:180px">
                <option value="">All Districts</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" @selected(request('district_id')==$d->id)>{{ $d->name }}</option>
                @endforeach
            </select>
            <select name="city_id" onchange="this.form.submit()" style="padding:9px 12px;border:1px solid #e6e9f0;border-radius:9px;font:inherit;min-width:160px">
                <option value="">All Cities</option>
                @foreach($cities as $c)
                    <option value="{{ $c->id }}" @selected(request('city_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="PIN or office name..." style="padding:9px 12px;border:1px solid #e6e9f0;border-radius:9px;font:inherit;min-width:200px">
            <button type="submit" style="padding:9px 16px;border:0;border-radius:9px;background:#5c4bb7;color:#fff;font:inherit;cursor:pointer">Search</button>
        </form>

        @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

        <div class="appointment-board">
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <thead><tr style="text-align:left;border-bottom:1px solid #eceef4;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#69758b">
                    <th style="padding:11px 14px">PIN Code</th>
                    <th style="padding:11px 14px">Post Office</th>
                    <th style="padding:11px 14px">Type</th>
                    <th style="padding:11px 14px">City</th>
                    <th style="padding:11px 14px">District</th>
                    <th style="padding:11px 14px">State</th>
                    <th style="padding:11px 14px">Status</th>
                    <th style="padding:11px 14px"></th>
                </tr></thead>
                <tbody>
                @forelse($pincodes as $pin)
                <tr style="border-bottom:1px solid #f0f2f7">
                    <td style="padding:13px 14px;font-weight:800;font-family:monospace;font-size:15px">{{ $pin->pincode }}</td>
                    <td style="padding:13px 14px;font-weight:600">{{ $pin->post_office_name }}</td>
                    <td style="padding:13px 14px;color:#69758b;font-size:12px">{{ $pin->office_type ?? '—' }}</td>
                    <td style="padding:13px 14px;color:#69758b">{{ $pin->city?->name ?? '—' }}</td>
                    <td style="padding:13px 14px;color:#69758b">{{ $pin->district->name }}</td>
                    <td style="padding:13px 14px;color:#69758b">{{ $pin->state->code }}</td>
                    <td style="padding:13px 14px"><span class="status {{ $pin->status === 'active' ? 'status-confirmed' : 'status-cancelled' }}">{{ ucfirst($pin->status) }}</span></td>
                    <td style="padding:13px 14px;text-align:right"><a href="{{ route('admin.location.pincodes.edit', $pin) }}" style="color:#5542ad;font-size:13px;font-weight:700">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding:30px;text-align:center;color:#69758b">No PIN codes found. Use <a href="{{ route('admin.location.sync') }}" style="color:#5542ad;font-weight:700">Sync India Data</a> to import.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px">{{ $pincodes->links() }}</div>
    </div>
</div>
@endsection
