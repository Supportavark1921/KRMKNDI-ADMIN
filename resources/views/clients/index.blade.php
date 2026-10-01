@extends('layouts.app', ['title' => 'Clients - ARK Jyotish'])

@section('content')
<main class="dashboard appointments-page admin-appointments">
    <header class="topbar">
        <div class="page-heading"><span>Client management</span><small>All registered members and their profiles</small></div>
        <a class="topbar-link" href="{{ route('dashboard') }}">Dashboard</a>
    </header>

    <section class="appointments-hero">
        <div>
            <span class="hero-overline">Pandit Ji workspace</span>
            <h1>Your client directory.</h1>
            <p>View every registered member, their birth details, and full appointment history in one place.</p>
        </div>
        <div class="admin-hero-panel">
            <span>Total clients</span>
            <strong>{{ $clients->count() }}</strong>
            <small>registered members</small>
        </div>
    </section>

    <section class="appointment-board">
        <div class="board-header">
            <div><h2>All clients</h2><p>{{ $clients->count() }} member{{ $clients->count() !== 1 ? 's' : '' }} registered</p></div>
            <span class="board-badge">Directory</span>
        </div>

        @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif

        <div class="appointment-list" style="margin-top:14px">
        @forelse($clients as $client)
            <article class="appointment-row" style="display:grid;grid-template-columns:48px minmax(0,1fr) 120px;gap:18px;padding:18px 10px;border:0;border-bottom:1px solid #edf0f5;border-radius:0;align-items:center">
                <div class="client-avatar" style="width:42px;height:42px;font-size:15px">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
                <div>
                    <a href="{{ route('clients.show', $client) }}" style="font-size:15px;font-weight:700;color:#15233d">{{ $client->name }}</a>
                    <p style="margin:3px 0 0;font-size:13px;color:#69758b">{{ $client->email }}</p>
                    @if($client->clientProfile?->birth_date)
                        <p style="margin:3px 0 0;font-size:12px;color:#8a95a9">DOB: {{ \Carbon\Carbon::parse($client->clientProfile->birth_date)->format('d M Y') }}@if($client->clientProfile->birth_place) &nbsp;·&nbsp; {{ $client->clientProfile->birth_place }}@endif</p>
                    @endif
                </div>
                <div style="text-align:right">
                    <span style="display:block;font-size:20px;font-weight:800;color:#15233d">{{ $client->appointments_count }}</span>
                    <small style="color:#69758b;font-size:11px">appointment{{ $client->appointments_count !== 1 ? 's' : '' }}</small>
                    <a href="{{ route('clients.show', $client) }}" style="display:block;margin-top:6px;font-size:12px;font-weight:750;color:#5542ad">View →</a>
                </div>
            </article>
        @empty
            <div class="empty-state appointment-empty">
                <span>◉</span>
                <h3>No clients yet</h3>
                <p>Registered members will appear here once they sign up.</p>
            </div>
        @endforelse
        </div>
    </section>
</main>
@endsection
