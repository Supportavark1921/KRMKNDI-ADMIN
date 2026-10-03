@extends('layouts.app')
@section('title', 'Audit Detail')
@section('content')
<style>
.aud-show-page{background:radial-gradient(circle at 92% 4%,#fff8e1 0,transparent 22%),#fdfaf2!important}
.aud-show-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:860px;margin:35px auto 28px;padding:30px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#ffd43b 0,transparent 26%),linear-gradient(118deg,#3d2a00,#8a6000)}
.aud-show-hero h1{margin:6px 0 0;font-size:26px;color:#fff}
.aud-show-hero p{margin:5px 0 0;color:#ffe9a8;font-size:13px}
.aud-overline{color:#ffd787;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.aud-card{max-width:860px;margin:0 auto 16px;background:#fff;border:1px solid #f0e8d0;border-radius:16px;padding:24px 28px}
.aud-card h3{margin:0 0 16px;font-size:13px;font-weight:800;color:#8a6000;letter-spacing:.06em;text-transform:uppercase}
.aud-changes{width:100%;border-collapse:collapse;font-size:13px}
.aud-changes th{padding:9px 12px;font-size:11px;font-weight:800;color:#9a8060;letter-spacing:.06em;text-transform:uppercase;border-bottom:2px solid #f0e8d0;text-align:left}
.aud-changes td{padding:11px 12px;border-bottom:1px solid #f8f3e8;vertical-align:top}
.aud-changes tr:last-child td{border-bottom:0}
.aud-changes tr:hover td{background:#fffdf5}
.aud-field{font-weight:700;color:#3d2a00;font-size:12px}
.aud-old{color:#b04040;font-family:monospace;font-size:12px;word-break:break-all}
.aud-new{color:#276946;font-family:monospace;font-size:12px;word-break:break-all}
.aud-btn{display:inline-flex;align-items:center;padding:10px 16px;border-radius:9px;color:#555e7a;background:#eef0f8;font:700 13px inherit;text-decoration:none}
.aud-btn:hover{opacity:.85}
.badge-created{background:#d3f9d8;color:#1a4d2a}
.badge-updated{background:#dbe4ff;color:#1c3a8a}
.badge-deleted{background:#fce8e8;color:#962a2a}
</style>

<div class="dashboard aud-show-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small><a href="{{ route('admin.audit.index') }}" style="color:inherit">Audit Log</a> / #{{ $activity->id }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="aud-show-hero">
        <div>
            <span class="aud-overline">Audit Log · Detail</span>
            <h1>#{{ $activity->id }} — {{ ucfirst($activity->event ?? $activity->description) }}</h1>
            <p>{{ $activity->created_at->format('d M Y H:i:s') }} · {{ $activity->causer?->name ?? 'System' }}</p>
        </div>
        <div style="font-size:48px">📋</div>
    </div>

    <div class="aud-card">
        <h3>Event Details</h3>
        <dl class="detail-list">
            <dt>When</dt><dd>{{ $activity->created_at->format('d M Y H:i:s') }}</dd>
            <dt>By</dt><dd>{{ $activity->causer?->name ?? 'System' }}{{ $activity->causer ? ' ('.$activity->causer->email.')' : '' }}</dd>
            <dt>Event</dt><dd>
                @php $ev = $activity->event ?? $activity->description; @endphp
                <span class="badge badge-{{ $ev }}" style="font-size:12px">{{ ucfirst($ev) }}</span>
            </dd>
            <dt>Model</dt><dd style="font-family:monospace;font-size:12px">{{ class_basename($activity->subject_type ?? 'n/a') }} #{{ $activity->subject_id }}</dd>
            @if($activity->subject)
            <dt>Subject</dt><dd>{{ $activity->subject?->name ?? '(deleted)' }}</dd>
            @endif
        </dl>
    </div>

    @if($activity->properties->isNotEmpty())
    <div class="aud-card">
        <h3>Changes</h3>
        @php
            $old = $activity->properties->get('old', []);
            $new = $activity->properties->get('attributes', $activity->properties->except(['old'])->toArray());
        @endphp
        <table class="aud-changes">
            <thead><tr><th>Field</th><th>Old value</th><th>New value</th></tr></thead>
            <tbody>
            @foreach($new as $field => $value)
            @if($field !== 'old')
            <tr>
                <td><span class="aud-field">{{ $field }}</span></td>
                <td><span class="aud-old">{{ is_array($old[$field] ?? null) ? json_encode($old[$field]) : ($old[$field] ?? '—') }}</span></td>
                <td><span class="aud-new">{{ is_array($value) ? json_encode($value) : $value }}</span></td>
            </tr>
            @endif
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="max-width:860px;margin:0 auto">
        <a href="{{ route('admin.audit.index') }}" class="aud-btn">← Back to log</a>
    </div>
</div>
@endsection
