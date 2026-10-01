@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.sync') }}">⟳ Sync India Data</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>

    <div style="max-width:1000px;margin:32px auto 0">
        <div class="section-heading" style="margin-bottom:20px">
            <div><span style="font-size:22px;font-weight:800">Countries</span><small style="color:#69758b;font-size:13px">Location master data</small></div>
            <a class="primary-link" href="{{ route('admin.location.countries.create') }}">+ Add Country</a>
        </div>

        @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

        <div class="appointment-board">
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <thead><tr style="text-align:left;border-bottom:1px solid #eceef4;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#69758b">
                    <th style="padding:11px 14px">Country</th>
                    <th style="padding:11px 14px">ISO</th>
                    <th style="padding:11px 14px">Phone</th>
                    <th style="padding:11px 14px">Currency</th>
                    <th style="padding:11px 14px">States/UTs</th>
                    <th style="padding:11px 14px">Status</th>
                    <th style="padding:11px 14px"></th>
                </tr></thead>
                <tbody>
                @forelse($countries as $country)
                <tr style="border-bottom:1px solid #f0f2f7">
                    <td style="padding:13px 14px;font-weight:700">{{ $country->name }}</td>
                    <td style="padding:13px 14px;font-family:monospace">{{ $country->iso_code }}</td>
                    <td style="padding:13px 14px;color:#69758b">{{ $country->phone_code ?? '—' }}</td>
                    <td style="padding:13px 14px;color:#69758b">{{ $country->currency_code ?? '—' }}</td>
                    <td style="padding:13px 14px">
                        <a href="{{ route('admin.location.states.index', ['country_id' => $country->id]) }}" style="color:#5542ad;font-weight:700">{{ $country->states_count }}</a>
                    </td>
                    <td style="padding:13px 14px"><span class="status {{ $country->status === 'active' ? 'status-confirmed' : 'status-cancelled' }}">{{ ucfirst($country->status) }}</span></td>
                    <td style="padding:13px 14px;text-align:right"><a href="{{ route('admin.location.countries.edit', $country) }}" style="color:#5542ad;font-size:13px;font-weight:700">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding:30px;text-align:center;color:#69758b">No countries yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px">{{ $countries->links() }}</div>
    </div>
</div>
@endsection
