@extends('layouts.app')
@section('title', 'Audit Detail')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">Audit Detail</h1>
        <p class="store-sub"><a href="{{ route('admin.audit.index') }}">Audit Log</a> / #{{ $activity->id }}</p>
    </div>
</div>

<div class="store-card" style="max-width:780px">
    <dl class="detail-list">
        <dt>When</dt><dd>{{ $activity->created_at->format('d M Y H:i:s') }}</dd>
        <dt>By</dt><dd>{{ $activity->causer?->name ?? 'System' }} {{ $activity->causer ? "({$activity->causer->email})" : '' }}</dd>
        <dt>Event</dt><dd>{{ $activity->event ?? $activity->description }}</dd>
        <dt>Model</dt><dd>{{ class_basename($activity->subject_type ?? 'n/a') }} #{{ $activity->subject_id }}</dd>
        @if($activity->subject)
        <dt>Subject</dt><dd>{{ $activity->subject?->name ?? '(deleted)' }}</dd>
        @endif
    </dl>

    @if($activity->properties->isNotEmpty())
    <h3 style="margin:20px 0 10px">Changes</h3>
    @php
        $old = $activity->properties->get('old', []);
        $new = $activity->properties->get('attributes', $activity->properties->except(['old'])->toArray());
    @endphp
    <table class="store-table">
        <thead><tr><th>Field</th><th>Old value</th><th>New value</th></tr></thead>
        <tbody>
        @foreach($new as $field => $value)
        @if($field !== 'old')
        <tr>
            <td><strong>{{ $field }}</strong></td>
            <td style="color:#888">{{ is_array($old[$field] ?? null) ? json_encode($old[$field]) : ($old[$field] ?? '—') }}</td>
            <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
        </tr>
        @endif
        @endforeach
        </tbody>
    </table>
    @endif

    <div style="margin-top:16px">
        <a href="{{ route('admin.audit.index') }}" class="btn-secondary">← Back to log</a>
    </div>
</div>
@endsection
