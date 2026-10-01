@extends('layouts.app', ['title' => 'Matajis'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🕉 Matajis</span><small>Manage Mataji / temple records</small></div>
    <div style="display:flex;gap:10px">
        <a class="nav-link topbar-link" href="{{ route('admin.store.matajis.create') }}" style="background:#f0ecff;color:#5c4bb7">+ Add Mataji</a>
        <a class="nav-link topbar-link" href="{{ route('dashboard') }}">Dashboard</a>
    </div>
</header>

<div style="max-width:1100px;margin:30px auto;padding:0 20px 80px">

@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="notice error">{{ session('error') }}</div>@endif

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;overflow:hidden">
    <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
            <tr style="background:#f9f9fb;border-bottom:1px solid #e6e8f0">
                <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Mataji / Temple</th>
                <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Location</th>
                <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Offering</th>
                <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Status</th>
                <th style="padding:12px 16px"></th>
            </tr>
        </thead>
        <tbody>
        @forelse($matajis as $m)
            <tr style="border-bottom:1px solid #f0f2f8">
                <td style="padding:14px 16px">
                    <div style="display:flex;align-items:center;gap:12px">
                        @if($m->image)
                            <img src="{{ asset('storage/'.$m->image) }}" style="width:38px;height:38px;border-radius:10px;object-fit:cover">
                        @else
                            <div style="width:38px;height:38px;border-radius:10px;background:#f0ecff;display:grid;place-items:center;font-size:18px">🕉</div>
                        @endif
                        <div>
                            <b style="display:block;color:#15233d">{{ $m->name }}</b>
                            <small style="color:#8a9ab8">{{ $m->temple_name }}</small>
                        </div>
                    </div>
                </td>
                <td style="padding:14px 16px;color:#536078">{{ implode(', ', array_filter([$m->city, $m->state])) ?: '—' }}</td>
                <td style="padding:14px 16px">
                    <span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;{{ $m->offering_available ? 'background:#e4f6ea;color:#276946' : 'background:#f3f5f9;color:#8a9ab8' }}">
                        {{ $m->offering_available ? 'Available' : 'Unavailable' }}
                    </span>
                </td>
                <td style="padding:14px 16px">
                    <span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;{{ $m->status === 'active' ? 'background:#e4f6ea;color:#276946' : 'background:#fdeaea;color:#c0392b' }}">
                        {{ ucfirst($m->status) }}
                    </span>
                </td>
                <td style="padding:14px 16px;text-align:right">
                    <a href="{{ route('admin.store.matajis.edit', $m) }}" style="color:#5c4bb7;font-weight:700;font-size:12px;margin-right:12px">Edit</a>
                    <form method="POST" action="{{ route('admin.store.matajis.destroy', $m) }}" style="display:inline" onsubmit="return confirm('Delete this Mataji?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="border:none;background:none;color:#c0392b;font-weight:700;font-size:12px;cursor:pointer">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" style="padding:40px;text-align:center;color:#8a9ab8">No Matajis yet. <a href="{{ route('admin.store.matajis.create') }}">Add one</a>.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $matajis->links() }}</div>
</div>
</main>
@endsection
