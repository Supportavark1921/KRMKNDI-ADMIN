@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.states.index') }}">← States</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>

    <div style="max-width:1100px;margin:32px auto 0">
        <div class="section-heading" style="margin-bottom:16px">
            <div><span style="font-size:22px;font-weight:800">Districts</span></div>
            <a class="primary-link" href="{{ route('admin.location.districts.create') }}">+ Add District</a>
        </div>

        <form method="get" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
            <select name="state_id" onchange="this.form.submit()" style="padding:9px 12px;border:1px solid #e6e9f0;border-radius:9px;font:inherit;min-width:180px">
                <option value="">All States</option>
                @foreach($states as $s)
                    <option value="{{ $s->id }}" @selected(request('state_id')==$s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search district..." style="padding:9px 12px;border:1px solid #e6e9f0;border-radius:9px;font:inherit;min-width:200px">
            <button type="submit" style="padding:9px 16px;border:0;border-radius:9px;background:#5c4bb7;color:#fff;font:inherit;cursor:pointer">Search</button>
        </form>

        @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

        <div class="appointment-board">
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <thead><tr style="text-align:left;border-bottom:1px solid #eceef4;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#69758b">
                    <th style="padding:11px 14px">District</th>
                    <th style="padding:11px 14px">State</th>
                    <th style="padding:11px 14px">Cities</th>
                    <th style="padding:11px 14px">Status</th>
                    <th style="padding:11px 14px"></th>
                </tr></thead>
                <tbody>
                @forelse($districts as $district)
                <tr style="border-bottom:1px solid #f0f2f7">
                    <td style="padding:13px 14px;font-weight:700">{{ $district->name }}</td>
                    <td style="padding:13px 14px;color:#69758b">{{ $district->state->name }} <span style="font-size:11px;font-family:monospace;color:#aab1bf">({{ $district->state->code }})</span></td>
                    <td style="padding:13px 14px">
                        <a href="{{ route('admin.location.cities.index', ['district_id' => $district->id]) }}" style="color:#5542ad;font-weight:700">{{ $district->cities_count }}</a>
                    </td>
                    <td style="padding:13px 14px"><span class="status {{ $district->status === 'active' ? 'status-confirmed' : 'status-cancelled' }}">{{ ucfirst($district->status) }}</span></td>
                    <td style="padding:13px 14px;text-align:right"><a href="{{ route('admin.location.districts.edit', $district) }}" style="color:#5542ad;font-size:13px;font-weight:700">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:30px;text-align:center;color:#69758b">No districts found. Import data or add manually.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px">{{ $districts->links() }}</div>
    </div>
</div>
@endsection
