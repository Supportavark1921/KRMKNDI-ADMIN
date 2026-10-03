@extends('layouts.app', ['title' => 'Appointments - ARK Jyotish'])
@section('content')
@php
    $role = auth()->user()->role;
    $isStaff = in_array($role, ['admin','manager','support','guruji']);
    $pendingCount = $appointments->where('status','pending')->count();
    $confirmedCount = $appointments->where('status','confirmed')->count();
@endphp
<style>
.apt-page{background:radial-gradient(circle at 84% 3%,#dcefe9 0,transparent 23%),#f5f8f7!important}
.apt-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1100px;margin:35px auto 24px;padding:34px 42px;border-radius:22px;color:#fff;background:radial-gradient(circle at 63% 0%,#32645c 0,transparent 31%),radial-gradient(circle at 96% 95%,#ba8740 0,transparent 27%),linear-gradient(120deg,#0d2829,#164442)}
.apt-hero h1{margin:8px 0 0;font-size:30px;color:#fff}
.apt-hero p{margin:6px 0 0;color:#b2d4ce;font-size:14px;max-width:520px}
.apt-overline{color:#f8ca73;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.apt-focus{display:grid;min-width:170px;gap:3px;padding:16px 18px;border:1px solid #ffffff29;border-radius:15px;background:#ffffff12;backdrop-filter:blur(6px)}
.apt-focus span,.apt-focus small{color:#d5e7e1;font-size:11px}
.apt-focus strong{color:#ffcf78;font-size:25px}
.apt-focus a{margin-top:6px;color:#fff;font-size:12px;font-weight:800}
.apt-book-btn{display:inline-flex;align-items:center;gap:8px;padding:14px 18px;border-radius:12px;color:#3d2c5c;background:#ffdc7d;font:800 14px inherit;box-shadow:0 11px 25px #08142f2e}

.apt-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;max-width:1100px;margin:0 auto 22px}
.apt-stat{display:flex;align-items:center;gap:12px;padding:17px 20px;border:1px solid #dce8e2;border-radius:15px;background:#fbfefd}
.apt-stat-icon{display:grid;width:36px;height:36px;place-items:center;border-radius:10px;font-weight:800;font-size:16px}
.apt-stat-icon.all{color:#16644f;background:#dff2e9}
.apt-stat-icon.pending{color:#9b6613;background:#fff0d3}
.apt-stat-icon.confirmed{color:#fff;background:#278467}
.apt-stat strong,.apt-stat small{display:block}
.apt-stat strong{font-size:22px;color:#0d2829}
.apt-stat small{font-size:12px;color:#6b8c80}

.apt-board{max-width:1100px;margin:0 auto;padding:26px 28px;border:1px solid #dce6e2;border-radius:20px;background:#fff;box-shadow:0 18px 45px #17463c0d}
.apt-board-head{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:0 2px 20px;border-bottom:1px solid #e1ebe7}
.apt-board-head h2{margin:0;font-size:20px;color:#0d2829}
.apt-board-head p{margin:5px 0 0;color:#6b8c80;font-size:13px}
.apt-board-badge{padding:6px 9px;border-radius:20px;color:#1d745b;background:#e5f5ed;font-size:11px;font-weight:750}

.apt-list{display:grid;gap:0;margin-top:10px}
.apt-row{display:grid;grid-template-columns:68px minmax(0,1fr) 160px;gap:18px;padding:20px 10px;border-bottom:1px solid #edf0f5;align-items:start}
.apt-row:last-child{border-bottom:0}
.apt-row:hover{border-radius:12px;background:#f6fbf8}

.apt-date{display:grid;place-items:center;padding:8px;border-radius:11px;color:#17644f;background:#e4f4ec;text-align:center}
.apt-date strong{font-size:22px;display:block}
.apt-date span{font-size:12px;font-weight:700}
.apt-date small{font-size:10px;font-weight:750;color:#629c8a;text-transform:uppercase}

.apt-info h3{margin:2px 0 5px;font-size:15px;color:#0d2829}
.apt-info p{display:flex;align-items:center;gap:7px;margin:0;color:#536078;font-size:13px}
.apt-service{color:#297a65;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
.apt-meta{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
.apt-meta span{padding:3px 7px;border-radius:6px;color:#738097;background:#f3f5f9;font-size:10px;font-weight:700}
.apt-dot{width:6px;height:6px;border-radius:50%;background:#d89934;display:inline-block}
.apt-client{display:flex;align-items:center;gap:8px;margin-top:10px}
.apt-avatar{display:grid;width:28px;height:28px;place-items:center;border-radius:50%;color:#fff;background:#367e6b;font-size:11px;font-weight:800;flex-shrink:0}
.apt-client b,.apt-client small{display:block}
.apt-client b{color:#2b3b4f;font-size:13px}
.apt-client small{color:#7b8798;font-size:11px}
.apt-note{margin-top:8px!important;color:#7e7591!important;font-style:italic;font-size:13px}

.apt-action{display:flex;flex-direction:column;align-items:flex-end;gap:8px}
.apt-status{display:inline-block;padding:5px 9px;border-radius:20px;font-size:12px;font-weight:750}
.apt-status.pending{color:#8a5a11;background:#fff3d7}
.apt-status.confirmed{color:#17664d;background:#dff3e8}
.apt-status.completed{color:#225e85;background:#e1f1fb}
.apt-status.cancelled{color:#984546;background:#fce9e9}
.apt-status-form{display:grid;width:100%;gap:6px;padding:10px 11px;border:1px solid #e1e7e4;border-radius:10px;background:#f9fcfa;text-align:left}
.apt-status-form label{margin:0;color:#63736e;font-size:10px;font-weight:800;letter-spacing:.04em;text-transform:uppercase}
.apt-status-form select{width:100%;padding:7px 9px;border:1px solid #c8ded5;border-radius:8px;color:#205c4e;background:#f7fcf9;font:600 13px inherit}
.apt-save{width:100%;padding:8px;border:0;border-radius:8px;color:#fff;background:#237861;font:700 12px inherit;cursor:pointer}
.apt-book-again{color:#6954af;font-size:12px;font-weight:750;margin-top:4px}

.apt-empty{padding:40px;text-align:center;border:1px dashed #c8ded5;border-radius:14px;background:#f5fbf8;margin-top:16px}
.apt-empty span{display:grid;width:46px;height:46px;margin:0 auto 12px;place-items:center;border-radius:14px;color:#17644f;background:#dff3e8;font-size:22px}
.apt-empty h3{margin:0 0 6px;color:#0d2829}
.apt-empty p{margin:0;color:#6b8c80;font-size:14px}

.flash-success{max-width:1100px;margin:0 auto 16px;padding:12px 16px;border-radius:12px;background:#d3f9d8;color:#1a4d2a;font-size:14px;font-weight:600}

@media(max-width:800px){.apt-stats{grid-template-columns:1fr 1fr}.apt-row{grid-template-columns:56px 1fr;}.apt-action{grid-column:1/-1;align-items:flex-start}}
@media(max-width:500px){.apt-stats{grid-template-columns:1fr}.apt-hero{padding:26px 22px}.apt-hero h1{font-size:24px}}
</style>

<div class="dashboard apt-page">
    <div class="topbar">
        <div class="page-heading">
            <span>{{ $isStaff ? 'Appointment Management' : 'My Appointments' }}</span>
            <small>{{ $isStaff ? 'Review and manage all booking requests' : 'Your sessions and booking requests' }}</small>
        </div>
        @if($isStaff)
            <a class="topbar-link" href="{{ route('dashboard') }}" style="background:#e2f3ec;color:#146650">Dashboard</a>
        @else
            <a class="topbar-link" href="{{ route('appointments.create') }}" style="background:#e2f3ec;color:#146650">+ New booking</a>
        @endif
    </div>

    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

    {{-- Hero --}}
    <div class="apt-hero">
        <div>
            <span class="apt-overline">
                {{ $role === 'guruji' ? 'Guruji Workspace' : ($isStaff ? 'Staff Workspace' : 'Your Booking Centre') }}
            </span>
            <h1>{{ $isStaff ? 'Stay ahead of every request.' : 'Your time with Pandit Ji.' }}</h1>
            <p>{{ $isStaff ? 'Confirm requests, update status and keep every consultation organised.' : 'Track each request from booking through confirmation.' }}</p>
        </div>
        @if($isStaff)
            <div class="apt-focus">
                <span>Today's focus</span>
                <strong>{{ $pendingCount }}</strong>
                <small>request{{ $pendingCount !== 1 ? 's' : '' }} pending review</small>
                <a href="#apt-list">Review now ↓</a>
            </div>
        @else
            <a class="apt-book-btn" href="{{ route('appointments.create') }}">＋ Book an appointment</a>
        @endif
    </div>

    {{-- Stats --}}
    <div class="apt-stats">
        <div class="apt-stat">
            <span class="apt-stat-icon all">◷</span>
            <div><strong>{{ $appointments->count() }}</strong><small>{{ $isStaff ? 'Total requests' : 'All bookings' }}</small></div>
        </div>
        <div class="apt-stat">
            <span class="apt-stat-icon pending">◌</span>
            <div><strong>{{ $pendingCount }}</strong><small>Pending review</small></div>
        </div>
        <div class="apt-stat">
            <span class="apt-stat-icon confirmed">✓</span>
            <div><strong>{{ $confirmedCount }}</strong><small>Confirmed</small></div>
        </div>
    </div>

    {{-- Board --}}
    <div class="apt-board" id="apt-list">
        <div class="apt-board-head">
            <div>
                <h2>{{ $isStaff ? 'All Appointment Requests' : 'Booking History' }}</h2>
                <p>{{ $appointments->count() ? $appointments->count().' appointment'.($appointments->count() !== 1 ? 's' : '').' shown' : 'No appointments yet.' }}</p>
            </div>
            <span class="apt-board-badge">{{ $isStaff ? 'Live schedule' : 'Private' }}</span>
        </div>

        <div class="apt-list">
        @forelse($appointments as $appointment)
            <div class="apt-row">
                {{-- Date --}}
                <div class="apt-date">
                    <strong>{{ $appointment->appointment_date->format('d') }}</strong>
                    <span>{{ $appointment->appointment_date->format('M') }}</span>
                    <small>{{ $appointment->appointment_date->format('D') }}</small>
                </div>

                {{-- Info --}}
                <div class="apt-info">
                    <span class="apt-service">{{ $appointment->service }}</span>
                    <h3>{{ $appointment->appointment_date->format('l, d F Y') }}</h3>
                    <p><span class="apt-dot"></span> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                    <div class="apt-meta">
                        <span>#ARK-{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span>Requested {{ $appointment->created_at->format('d M Y') }}</span>
                    </div>
                    @if($isStaff)
                        <div class="apt-client">
                            <div class="apt-avatar">{{ strtoupper(substr($appointment->user?->name ?? $appointment->name ?? '?', 0, 1)) }}</div>
                            <div>
                                <b>{{ $appointment->user?->name ?? $appointment->name ?? '—' }}</b>
                                <small>{{ $appointment->phone }}@if($appointment->user?->email) · {{ $appointment->user->email }}@endif</small>
                            </div>
                        </div>
                    @else
                        <div class="apt-meta" style="margin-top:6px"><span>📞 {{ $appointment->phone }}</span></div>
                    @endif
                    @if($appointment->notes)
                        <p class="apt-note">"{{ $appointment->notes }}"</p>
                    @endif
                </div>

                {{-- Action --}}
                <div class="apt-action">
                    <span class="apt-status {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                    @if($isStaff)
                        <form class="apt-status-form" method="POST" action="{{ route('appointments.status', $appointment) }}">
                            @csrf @method('PATCH')
                            <label for="s{{ $appointment->id }}">Update status</label>
                            <select id="s{{ $appointment->id }}" name="status">
                                <option value="pending"   @selected($appointment->status==='pending')>Pending</option>
                                <option value="confirmed" @selected($appointment->status==='confirmed')>Confirmed</option>
                                <option value="completed" @selected($appointment->status==='completed')>Completed</option>
                                <option value="cancelled" @selected($appointment->status==='cancelled')>Cancelled</option>
                            </select>
                            <button class="apt-save" type="submit">Save</button>
                        </form>
                    @else
                        <a class="apt-book-again" href="{{ route('appointments.create') }}">Book another →</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="apt-empty">
                <span>☼</span>
                <h3>{{ $isStaff ? 'No booking requests yet' : 'Your calendar is open' }}</h3>
                <p>{{ $isStaff ? 'New requests will appear here.' : 'Choose a time to receive guidance from Pandit Ji.' }}</p>
                @if(!$isStaff)
                    <a class="primary-link" href="{{ route('appointments.create') }}" style="margin-top:14px">Book your first appointment</a>
                @endif
            </div>
        @endforelse
        </div>
    </div>
</div>
@endsection
