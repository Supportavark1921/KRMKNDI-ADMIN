@extends('layouts.app', ['title' => 'Availability - ARK Jyotish'])

@section('content')
<main class="dashboard availability-page">
    <header class="topbar"><div class="page-heading"><span>Availability calendar</span><small>Set the booking hours clients can choose from</small></div><a class="topbar-link" href="{{ route('appointments.index') }}">View appointments</a></header>
    <section class="availability-hero"><div><span class="hero-overline">Pandit Ji workspace</span><h1>Plan your weekly schedule.</h1><p>Choose your working days, hours, and session duration. Booked or unavailable times are automatically hidden from members.</p></div><div class="availability-legend"><span><i class="open"></i> Accepting bookings</span><span><i class="closed"></i> Day off</span></div></section>
    @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
    <form method="POST" action="{{ route('availability.update') }}" class="availability-board">@csrf @method('PUT')
        <div class="board-header"><div><h2>Weekly working hours</h2><p>Changes apply immediately to new appointment bookings.</p></div><button class="availability-save">Save availability</button></div>
        <div class="schedule-head"><span>Day</span><span>Accept bookings</span><span>Working hours</span><span>Session length</span></div>
        @foreach($slots as $slot)
            <div class="schedule-row">
                <div class="day-name"><span class="day-round">{{ substr($days[$slot->weekday], 0, 1) }}</span><b>{{ $days[$slot->weekday] }}</b></div>
                <label class="switch"><input type="checkbox" name="slots[{{ $slot->weekday }}][is_available]" value="1" @checked($slot->is_available)><span></span><em>{{ $slot->is_available ? 'Open' : 'Closed' }}</em></label>
                <div class="hours"><input type="time" name="slots[{{ $slot->weekday }}][start_time]" value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}" required><span>to</span><input type="time" name="slots[{{ $slot->weekday }}][end_time]" value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}" required></div>
                <select name="slots[{{ $slot->weekday }}][slot_minutes]"><option value="30" @selected($slot->slot_minutes === 30)>30 minutes</option><option value="45" @selected($slot->slot_minutes === 45)>45 minutes</option><option value="60" @selected($slot->slot_minutes === 60)>1 hour</option><option value="90" @selected($slot->slot_minutes === 90)>90 minutes</option><option value="120" @selected($slot->slot_minutes === 120)>2 hours</option></select>
            </div>
        @endforeach
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
    </form>
</main>
@endsection
