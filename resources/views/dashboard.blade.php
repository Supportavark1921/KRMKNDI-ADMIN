@extends('layouts.app', ['title' => 'Dashboard - ARK Jyotish'])

@section('content')
<main class="dashboard">
    <header class="topbar">
        <div class="page-heading"><span>Dashboard</span><small>{{ now()->format('l, d F Y') }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </header>

    @if (auth()->user()->role === 'admin')
        <section class="dashboard-welcome admin-welcome">
            <div><span class="pill">Pandit Ji workspace</span><h1>Namaste, {{ auth()->user()->name }}.</h1><p>Here is your appointment overview for today. Review new requests and keep your schedule organised.</p></div>
            <a class="primary-link large-action" href="{{ route('appointments.index') }}">Manage appointments <span>→</span></a>
        </section>
        <section class="metric-grid">
            <article><span>Total appointments</span><strong>{{ $appointmentCounts['total'] }}</strong><small>All time bookings</small></article>
            <article><span>Needs review</span><strong>{{ $appointmentCounts['pending'] }}</strong><small>Pending requests</small></article>
            <article><span>Confirmed</span><strong>{{ $appointmentCounts['confirmed'] }}</strong><small>Upcoming or completed</small></article>
        </section>
    @else
        <section class="dashboard-welcome">
            <div><span class="pill">Your spiritual space</span><h1>Namaste, {{ auth()->user()->name }}.</h1><p>Seek guidance at the right time. Book a personal consultation, puja, or muhurat with Pandit Ji.</p></div>
            <a class="primary-link large-action" href="{{ route('appointments.create') }}">Book an appointment <span>→</span></a>
        </section>

        <section class="dashboard-grid">
            <article class="next-appointment">
                <div class="card-heading"><div><span class="section-kicker">Your next appointment</span><h2>{{ $nextAppointment ? $nextAppointment->service : 'No appointment booked' }}</h2></div><a href="{{ route('appointments.index') }}">View all</a></div>
                @if($nextAppointment)
                    <div class="next-details"><div class="date-tile"><strong>{{ $nextAppointment->appointment_date->format('d') }}</strong><span>{{ $nextAppointment->appointment_date->format('M') }}</span></div><div><b>{{ $nextAppointment->appointment_date->format('l, d M Y') }}</b><p>{{ \Carbon\Carbon::parse($nextAppointment->appointment_time)->format('g:i A') }} with Pandit Ji</p><span class="status status-{{ $nextAppointment->status }}">{{ ucfirst($nextAppointment->status) }}</span></div></div>
                @else
                    <p class="empty-copy">You do not have an upcoming session. Choose a service below to get started.</p><a class="secondary-link" href="{{ route('appointments.create') }}">Choose a time</a>
                @endif
            </article>
            <article class="help-card"><span class="help-icon">✦</span><h2>Need guidance?</h2><p>Share your preferred date, time, and the purpose of your consultation. Pandit Ji will confirm your request.</p><a href="{{ route('appointments.create') }}">How booking works →</a></article>
        </section>

        <section class="services-section"><div class="section-title"><div><span class="section-kicker">Book a service</span><h2>How may Pandit Ji help you?</h2></div><a href="{{ route('appointments.create') }}">See all services</a></div>
            <div class="service-grid">
                <a href="{{ route('appointments.create') }}" class="service-card"><span class="service-icon">☼</span><h3>Kundli consultation</h3><p>Personal guidance based on your birth chart.</p><span>Book now →</span></a>
                <a href="{{ route('appointments.create') }}" class="service-card"><span class="service-icon">⌁</span><h3>Puja booking</h3><p>Arrange a sacred puja for your family or home.</p><span>Book now →</span></a>
                <a href="{{ route('appointments.create') }}" class="service-card"><span class="service-icon">♡</span><h3>Marriage matching</h3><p>Thoughtful compatibility and kundli matching.</p><span>Book now →</span></a>
            </div>
        </section>
    @endif
</main>
@endsection
