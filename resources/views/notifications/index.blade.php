@extends('layouts.app', ['title' => 'Notifications - ARK Jyotish'])

@section('content')
<main class="dashboard notifications-page">
    <header class="topbar"><div class="page-heading"><span>Notifications</span><small>Updates about your appointments and requests</small></div><a class="topbar-link" href="{{ route('appointments.index') }}">Appointments</a></header>
    <section class="notifications-hero"><div><span class="hero-overline">Your update centre</span><h1>Stay in the loop.</h1><p>Every booking request and appointment update appears here, so you always know what happens next.</p></div><span class="bell-mark">♢</span></section>
    <section class="notifications-board">
        <div class="board-header"><div><h2>Recent updates</h2><p>{{ $notifications->whereNull('read_at')->count() }} unread notification{{ $notifications->whereNull('read_at')->count() === 1 ? '' : 's' }}</p></div>@if($notifications->whereNull('read_at')->count())<form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="read-all">Mark all as read</button></form>@endif</div>
        @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
        <div class="notification-list">@forelse($notifications as $notification)
            <form method="POST" action="{{ route('notifications.read', $notification) }}" class="notification-item {{ $notification->read_at ? 'is-read' : 'is-unread' }}">@csrf @method('PATCH')<button type="submit"><span class="notification-icon {{ $notification->data['kind'] ?? 'booking' }}">{{ ($notification->data['kind'] ?? '') === 'confirmed' ? '✓' : '✦' }}</span><span class="notification-copy"><b>{{ $notification->data['title'] }}</b><span>{{ $notification->data['message'] }}</span><small>{{ $notification->created_at->diffForHumans() }}</small></span>@if(!$notification->read_at)<i></i>@endif</button></form>
        @empty <div class="empty-state"><span>✦</span><h3>All caught up</h3><p>Your booking updates will appear here.</p></div>
        @endforelse</div>
    </section>
</main>
@endsection
