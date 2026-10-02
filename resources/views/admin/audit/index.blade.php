@extends('layouts.app')
@section('title', 'Audit Log')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">Audit Log</h1>
        <p class="store-sub">Read-only record of all create/update/delete actions.</p>
    </div>
</div>

<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
    <select name="event" class="form-input">
        <option value="">All events</option>
        @foreach($events as $ev)
        <option value="{{ $ev }}" @selected(request('event') === $ev)>{{ $ev }}</option>
        @endforeach
    </select>
    <input type="text" name="subject_type" value="{{ request('subject_type') }}" placeholder="Model (e.g. Product)" class="form-input" style="width:160px">
    <input type="date" name="from" value="{{ request('from') }}" class="form-input">
    <input type="date" name="to"   value="{{ request('to') }}" class="form-input">
    <button type="submit" class="btn-secondary">Filter</button>
    <a href="{{ route('admin.audit.index') }}" class="btn-secondary">Clear</a>
</form>

<div class="store-card">
    <table class="store-table">
        <thead><tr>
            <th>When</th><th>Who</th><th>Event</th><th>Model</th><th>Subject</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($activities as $a)
        <tr>
            <td style="white-space:nowrap;font-size:12px">{{ $a->created_at->format('d M Y H:i') }}</td>
            <td>{{ $a->causer?->name ?? '—' }}</td>
            <td><span class="badge badge-{{ $a->event === 'deleted' ? 'inactive' : 'active' }}">{{ $a->event ?? $a->description }}</span></td>
            <td style="font-size:12px">{{ class_basename($a->subject_type ?? '') }}</td>
            <td style="font-size:12px">{{ $a->subject?->name ?? $a->subject_id ?? '—' }}</td>
            <td><a href="{{ route('admin.audit.show', $a) }}" class="btn-sm">Detail</a></td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:24px;color:#888">No activity recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="padding:12px">{{ $activities->links() }}</div>
</div>
@endsection
