@extends('layouts.app', ['title' => 'Appointments - ARK Jyotish'])

@section('content')
@php
    $role = auth()->user()->role;
    $isStaff = in_array($role, ['admin','manager','support','guruji']);
    $isAdmin = in_array($role, ['admin','manager','support']);
    $pendingCount = $appointments->where('status', 'pending')->count();
    $confirmedCount = $appointments->where('status', 'confirmed')->count();
@endphp
<main class=”dashboard appointments-page {{ $isStaff ? 'admin-appointments' : 'member-appointments' }}”>
    <header class=”topbar”>
        <div class=”page-heading”><span>{{ $isStaff ? 'Appointment management' : 'My appointments' }}</span><small>{{ $isStaff ? 'Review and organise booking requests' : 'Your sessions and booking requests in one place' }}</small></div>
        @if($isStaff)<a class=”topbar-link” href=”{{ route('dashboard') }}”>Dashboard</a>@else<a class=”topbar-link” href=”{{ route('appointments.create') }}”>+ New booking</a>@endif
    </header>

    <section class=”appointments-hero”>
        <div><span class=”hero-overline”>{{ $role === 'guruji' ? 'Guruji workspace' : ($isAdmin ? 'Pandit Ji workspace' : 'Your booking centre') }}</span><h1>{{ $isStaff ? 'Stay ahead of every request.' : 'Your time with Pandit Ji.' }}</h1><p>{{ $isStaff ? 'Confirm a request, adjust its status, and keep every consultation and puja organised.' : 'Track each request from booking through confirmation. Need another session? You can book one in seconds.' }}</p></div>
        @if(!$isStaff)
            <a class=”hero-book-button” href=”{{ route('appointments.create') }}”><span>＋</span> Book an appointment</a>
        @else
            <div class=”admin-hero-panel”><span>Today's focus</span><strong>{{ $pendingCount }} request{{ $pendingCount !== 1 ? 's' : '' }}</strong><small>waiting for your review</small><a href=”#requests”>Review now ↓</a></div>
        @endif
    </section>

    <section class=”appointment-summary”>
        <article><span class=”summary-icon all”>◷</span><div><strong>{{ $appointments->count() }}</strong><small>{{ $isStaff ? 'Total requests' : 'All bookings' }}</small></div></article>
        <article><span class=”summary-icon pending”>◌</span><div><strong>{{ $pendingCount }}</strong><small>Pending review</small></div></article>
        <article><span class=”summary-icon confirmed”>✓</span><div><strong>{{ $confirmedCount }}</strong><small>Confirmed</small></div></article>
    </section>

    <section id=”requests” class=”appointment-board”>
        <div class=”board-header”><div><h2>{{ $isStaff ? 'All appointment requests' : 'Booking history' }}</h2><p>{{ $appointments->count() ? $appointments->count().' appointment'.($appointments->count() !== 1 ? 's' : '').' shown' : 'Your appointments will appear here.' }}</p></div><span class=”board-badge”>{{ $isStaff ? 'Live schedule' : 'Private' }}</span></div>
        @if(session('success'))<div class=”notice success”>{{ session('success') }}</div>@endif
        <div class=”appointment-list”>
        @forelse($appointments as $appointment)
            <article class=”appointment-row”>
                <div class=”appointment-date”><strong>{{ $appointment->appointment_date->format('d') }}</strong><span>{{ $appointment->appointment_date->format('M') }}</span><small>{{ $appointment->appointment_date->format('D') }}</small></div>
                <div class=”appointment-info”><span class=”service-label”>{{ $appointment->service }}</span><h3>{{ $appointment->appointment_date->format('l, d F Y') }}</h3><p><span class=”time-dot”></span>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                    <div class=”booking-meta”><span>Booking #ARK-{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}</span><span>Requested {{ $appointment->created_at->format('d M Y') }}</span></div>
                    @if($isStaff)<div class=”client-detail”><span class=”client-avatar”>{{ strtoupper(substr($appointment->user?->name ?? $appointment->name ?? '?', 0, 1)) }}</span><div><b>{{ $appointment->user?->name ?? $appointment->name ?? '—' }}</b><small>{{ $appointment->phone }} @if($appointment->user?->email)&nbsp;·&nbsp; {{ $appointment->user->email }}@endif</small></div></div>@else <div class=”member-detail”><span>Contact number</span><b>{{ $appointment->phone }}</b></div>@endif
                    @if($appointment->notes)<p class=”note”>”{{ $appointment->notes }}”</p>@endif
                </div>
                <div class=”appointment-action”><span class=”status status-{{ $appointment->status }}”>{{ ucfirst($appointment->status) }}</span>@if($isStaff)<form class=”status-form” method=”POST” action=”{{ route('appointments.status', $appointment) }}”>@csrf @method('PATCH')<label for=”status-{{ $appointment->id }}”>Appointment status</label><select id=”status-{{ $appointment->id }}” name=”status”><option value=”pending” @selected($appointment->status === 'pending')>Pending review</option><option value=”confirmed” @selected($appointment->status === 'confirmed')>Confirmed</option><option value=”completed” @selected($appointment->status === 'completed')>Completed</option><option value=”cancelled” @selected($appointment->status === 'cancelled')>Cancelled</option></select><button class=”status-save” type=”submit”>Update status</button></form>@else <a class=”book-again” href=”{{ route('appointments.create') }}”>Book another session →</a>@endif</div>
            </article>
        @empty
            <div class=”empty-state appointment-empty”><span>☼</span><h3>{{ $isStaff ? 'No booking requests yet' : 'Your calendar is open' }}</h3><p>{{ $isStaff ? 'New requests will arrive here.' : 'Choose a convenient time to receive guidance from Pandit Ji.' }}</p>@if(!$isStaff)<a class=”primary-link” href=”{{ route('appointments.create') }}”>Book your first appointment</a>@endif</div>
        @endforelse
        </div>
    </section>
</main>
@endsection
