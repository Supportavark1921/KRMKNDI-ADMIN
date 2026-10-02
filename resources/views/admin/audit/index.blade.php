@extends('layouts.app')
@section('title', 'Audit Log')
@section('content')
<style>
.aud-page{background:radial-gradient(circle at 92% 4%,#fff8e1 0,transparent 22%),#fdf9f2!important}
.aud-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1200px;margin:35px auto 22px;padding:38px 44px;border-radius:22px;color:#fff;background:radial-gradient(circle at 86% 10%,#fcc419 0,transparent 26%),linear-gradient(118deg,#3d2e00,#7a5c00)}
.aud-hero h1{margin:8px 0;font-size:38px;color:#fff}
.aud-hero p{max-width:600px;margin:0;color:#f5e8b8;line-height:1.6}
.aud-overline{color:#ffd787;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.aud-toolbar{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 18px;flex-wrap:wrap}
.aud-toolbar select,.aud-toolbar input{padding:10px 12px;border:1px solid #e6d98a;border-radius:11px;font:inherit;color:#3a3000;background:#fff;outline:none;transition:.2s}
.aud-toolbar select:focus,.aud-toolbar input:focus{border-color:#f59f00;box-shadow:0 0 0 3px #fff3bf}
.aud-board{max-width:1200px;margin:0 auto;border:1px solid #e8d878;border-radius:20px;background:#fff;box-shadow:0 18px 45px #3d2e0009;overflow:hidden}
.aud-table{width:100%;border-collapse:collapse}
.aud-table thead th{padding:13px 16px;border-bottom:1px solid #f5efc0;background:#fffef5;color:#7a6a00;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;text-align:left}
.aud-table thead th:last-child{text-align:right}
.aud-table tbody tr{border-bottom:1px solid #fdf8e0;transition:background .15s}
.aud-table tbody tr:last-child{border:0}
.aud-table tbody tr:hover{background:#fffef5}
.aud-table td{padding:14px 16px;vertical-align:middle}
.aud-causer{display:flex;align-items:center;gap:8px}
.aud-avatar{display:grid;width:30px;height:30px;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(135deg,#fcc419,#f08c00);font-size:11px;font-weight:800;flex-shrink:0}
.aud-event-created{display:inline-block;padding:3px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.aud-event-updated{display:inline-block;padding:3px 9px;border-radius:20px;color:#1e4d8c;background:#dbeafe;font-size:11px;font-weight:750}
.aud-event-deleted{display:inline-block;padding:3px 9px;border-radius:20px;color:#962a2a;background:#fce8e8;font-size:11px;font-weight:750}
.aud-event-other{display:inline-block;padding:3px 9px;border-radius:20px;color:#5a3a8e;background:#ede9ff;font-size:11px;font-weight:750}
.aud-model{display:inline-block;padding:3px 8px;border-radius:7px;background:#f5f0d8;color:#6a5800;font-size:11px;font-weight:700;font-family:monospace}
.action-cell{display:flex;align-items:center;justify-content:flex-end}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font-size:14px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#f59f00;color:#f08c00;background:#fff3bf}
.aud-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:72px 24px;text-align:center}
.aud-empty-icon{display:grid;width:60px;height:60px;place-items:center;border-radius:18px;background:#fff3bf;color:#f08c00;font-size:30px;margin:0 auto 16px}
.pagination-wrap{display:flex;justify-content:center;padding:20px;border-top:1px solid #f5eecc}
</style>

<div class="dashboard aud-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small>Audit Log</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="aud-hero">
        <div>
            <span class="aud-overline">Admin · Security</span>
            <h1>Audit Log</h1>
            <p>Read-only record of every create, update and archive action. Who changed what, and when.</p>
        </div>
        <div style="text-align:center">
            <div style="font-size:48px">📋</div>
            <div style="color:#ffd787;font-size:12px;font-weight:700;margin-top:4px">{{ $activities->total() }} entries</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.audit.index') }}" class="aud-toolbar">
        <select name="event">
            <option value="">All events</option>
            @foreach($events as $ev)
            <option value="{{ $ev }}" @selected(request('event') === $ev)>{{ ucfirst($ev) }}</option>
            @endforeach
        </select>
        <input type="text" name="subject_type" value="{{ request('subject_type') }}" placeholder="Model (e.g. Product)" style="width:160px">
        <input type="date" name="from" value="{{ request('from') }}">
        <input type="date" name="to"   value="{{ request('to') }}">
        <button type="submit" style="width:auto;margin:0;padding:10px 16px;border-radius:10px;background:#f59f00;color:#3d2e00;border:0;cursor:pointer;font:700 13px inherit">Filter</button>
        @if(request()->hasAny(['event','subject_type','from','to']))
        <a href="{{ route('admin.audit.index') }}" style="padding:10px 14px;border-radius:10px;border:1px solid #e6d98a;color:#7a6a00;font-size:13px;font-weight:600;text-decoration:none;background:#fff">Clear</a>
        @endif
    </form>

    <div class="aud-board">
        @if($activities->isEmpty())
        <div class="aud-empty">
            <div class="aud-empty-icon">📋</div>
            <h3>No activity recorded yet</h3>
            <p>Actions on managed records will appear here.</p>
        </div>
        @else
        <table class="aud-table">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Who</th>
                    <th>Event</th>
                    <th>Model</th>
                    <th>Subject</th>
                    <th style="text-align:right">Detail</th>
                </tr>
            </thead>
            <tbody>
            @foreach($activities as $a)
            <tr>
                <td style="white-space:nowrap;font-size:12px;color:#7a6a00">{{ $a->created_at->format('d M Y H:i') }}</td>
                <td>
                    <div class="aud-causer">
                        <div class="aud-avatar">{{ strtoupper(substr($a->causer?->name ?? '?', 0, 1)) }}</div>
                        <span style="font-size:13px">{{ $a->causer?->name ?? '—' }}</span>
                    </div>
                </td>
                <td>
                    @php $ev = $a->event ?? $a->description; @endphp
                    <span class="aud-event-{{ in_array($ev,['created','updated','deleted']) ? $ev : 'other' }}">{{ $ev }}</span>
                </td>
                <td><span class="aud-model">{{ class_basename($a->subject_type ?? '') }}</span></td>
                <td style="font-size:13px;color:#5a4a00">{{ $a->subject?->name ?? ('#'.$a->subject_id) ?? '—' }}</td>
                <td>
                    <div class="action-cell">
                        <a href="{{ route('admin.audit.show', $a) }}" class="btn-icon" title="View detail">👁</a>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @if($activities->hasPages())
        <div class="pagination-wrap">{{ $activities->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection
