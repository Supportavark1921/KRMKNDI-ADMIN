@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.countries.index') }}">← Countries</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>

    <div style="max-width:1100px;margin:32px auto 0">
        <div class="section-heading" style="margin-bottom:16px">
            <div><span style="font-size:22px;font-weight:800">States / Union Territories</span></div>
            <a class="primary-link" href="{{ route('admin.location.states.create') }}">+ Add State/UT</a>
        </div>

        <form method="get" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
            <select name="country_id" onchange="this.form.submit()" style="padding:9px 12px;border:1px solid #e6e9f0;border-radius:9px;font:inherit;min-width:160px">
                <option value="">All Countries</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" @selected(request('country_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search state..." style="padding:9px 12px;border:1px solid #e6e9f0;border-radius:9px;font:inherit;min-width:200px">
            <button type="submit" style="padding:9px 16px;border:0;border-radius:9px;background:#5c4bb7;color:#fff;font:inherit;cursor:pointer">Search</button>
        </form>

        @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

        <div class="appointment-board">
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <thead><tr style="text-align:left;border-bottom:1px solid #eceef4;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#69758b">
                    <th style="padding:11px 14px">State / UT</th>
                    <th style="padding:11px 14px">Code</th>
                    <th style="padding:11px 14px">Type</th>
                    <th style="padding:11px 14px">Country</th>
                    <th style="padding:11px 14px">Districts</th>
                    <th style="padding:11px 14px">Status</th>
                    <th style="padding:11px 14px"></th>
                </tr></thead>
                <tbody>
                @forelse($states as $state)
                <tr style="border-bottom:1px solid #f0f2f7">
                    <td style="padding:13px 14px;font-weight:700">{{ $state->name }}</td>
                    <td style="padding:13px 14px;font-family:monospace">{{ $state->code }}</td>
                    <td style="padding:13px 14px"><span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:750;{{ $state->type === 'UNION_TERRITORY' ? 'color:#5c4bb7;background:#f0ecff' : 'color:#276946;background:#e4f6ea' }}">{{ $state->typeLabel() }}</span></td>
                    <td style="padding:13px 14px;color:#69758b">{{ $state->country->name }}</td>
                    <td style="padding:13px 14px">
                        <a href="{{ route('admin.location.districts.index', ['state_id' => $state->id]) }}" style="color:#5542ad;font-weight:700">{{ $state->districts_count }}</a>
                    </td>
                    <td style="padding:13px 14px"><span class="status {{ $state->status === 'active' ? 'status-confirmed' : 'status-cancelled' }}">{{ ucfirst($state->status) }}</span></td>
                    <td style="padding:13px 14px;text-align:right"><a href="{{ route('admin.location.states.edit', $state) }}" style="color:#5542ad;font-size:13px;font-weight:700">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding:30px;text-align:center;color:#69758b">No states found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px">{{ $states->links() }}</div>
    </div>
</div>
@endsection
