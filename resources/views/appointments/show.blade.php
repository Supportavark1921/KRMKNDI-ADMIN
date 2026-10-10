@extends('layouts.app')
@section('title', 'Appointment #ARK-' . str_pad($appointment->id, 4, '0', STR_PAD_LEFT))
@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<style>
.don-page{background:radial-gradient(circle at 88% 5%,#e8f4ff 0,transparent 22%),#f4f6f8!important}
.don-detail-wrap{max-width:860px;margin:0 auto}
.don-detail-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin:32px auto 20px;padding:30px 36px;border-radius:20px;color:#fff;background:radial-gradient(circle at 82% 10%,#60a0f0 0,transparent 28%),linear-gradient(118deg,#0a2040,#1a5a90)}
.don-detail-hero h1{margin:6px 0;font-size:26px;color:#fff}
.don-detail-hero p{margin:0;color:#c8e0f8;font-size:13px}
.hero-overline{color:#85d0ff;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.detail-panel{padding:22px 26px;border:1px solid #dce8f0;border-radius:16px;background:#fff;box-shadow:0 4px 12px #1a5a9006}
.panel-title{font-size:11px;font-weight:800;color:#6080a0;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px;padding-bottom:11px;border-bottom:1px solid #e8f0f5}
.detail-row{margin-bottom:14px}
.detail-row:last-child{margin-bottom:0}
.d-label{font-size:11px;font-weight:700;color:#7090b0;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px}
.d-value{font-size:15px;font-weight:700;color:#102030}
.d-value.muted{font-weight:400;color:#405060;font-size:14px}
.breakdown-panel{padding:22px 26px;border:1px solid #dce8f0;border-radius:16px;background:#fff;margin-bottom:16px}
.breakdown-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #e8f0f5;font-size:14px}
.breakdown-row:last-child{border-bottom:0}
.breakdown-row .bl{color:#405060}
.breakdown-row .br{font-weight:700;color:#102030}
.breakdown-total{display:flex;justify-content:space-between;align-items:center;padding:14px 0 0;margin-top:4px;border-top:2px solid #d0e0ee;font-size:16px;font-weight:900}
.breakdown-total .bl{color:#102030}
.breakdown-total .br{color:#2a7ac0;font-size:20px}
.appt-id-big{font-family:ui-monospace,monospace;font-size:24px;font-weight:900;color:#7ad0ff;letter-spacing:.04em}
.st-confirmed{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:13px;font-weight:750}
.st-pending{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:13px;font-weight:750}
.st-completed{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1a4a80;background:#e3eeff;font-size:13px;font-weight:750}
.st-cancelled{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#606070;background:#f0f0f5;font-size:13px;font-weight:750}
.st-paid{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:13px;font-weight:750}
.st-unpaid{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:13px;font-weight:750}
.st-screenshot_uploaded{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1a4a80;background:#e3eeff;font-size:13px;font-weight:750}
.st-confirmed::before,.st-pending::before,.st-completed::before,.st-cancelled::before,.st-paid::before,.st-unpaid::before,.st-screenshot_uploaded::before{content:"";width:7px;height:7px;border-radius:50%}
.st-confirmed::before{background:#28a76a}.st-pending::before{background:#e69c3a}.st-completed::before{background:#1a6abf}.st-cancelled::before{background:#909090}.st-paid::before{background:#28a76a}.st-unpaid::before{background:#e69c3a}.st-screenshot_uploaded::before{background:#1a6abf}
.btn-back{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:10px;border:1px solid #dce8f0;color:#405060;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.btn-back:hover{background:#f0f6fc}
@media(max-width:650px){.detail-grid{grid-template-columns:1fr}}
</style>
<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span><a href="{{ route('appointments.index') }}" style="color:inherit;text-decoration:none">Appointments</a> › Detail</span><small>#ARK-{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="don-detail-wrap">
        <div class="don-detail-hero">
            <div>
                <span class="hero-overline">Appointment Detail</span>
                <h1 class="appt-id-big">#ARK-{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}</h1>
                <p>{{ $appointment->appointment_date->format('d M Y') }} · {{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->appointment_time)->format('g:i A') }} · {{ $appointment->service ?? 'Service' }}</p>
            </div>
            <span class="st-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
        </div>

        {{-- Booking summary (full width) --}}
        <div class="breakdown-panel">
            <p class="panel-title" style="margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #e8f0f5">Booking Summary</p>
            <div class="breakdown-row"><span class="bl">Service</span><span class="br">{{ $appointment->service ?? '—' }}</span></div>
            <div class="breakdown-row"><span class="bl">Date</span><span class="br">{{ $appointment->appointment_date->format('l, d M Y') }}</span></div>
            <div class="breakdown-row"><span class="bl">Time</span><span class="br">{{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->appointment_time)->format('g:i A') }}</span></div>
            @if($appointment->total_amount)
            <div class="breakdown-total"><span class="bl">Total Amount</span><span class="br">₹{{ number_format($appointment->total_amount, 2) }}</span></div>
            @endif
        </div>

        <div class="detail-grid">
            {{-- Client info --}}
            <div class="detail-panel">
                <p class="panel-title">Client Information</p>
                <div class="detail-row"><div class="d-label">Name</div><div class="d-value">{{ $appointment->name ?? $appointment->user?->name ?? '—' }}</div></div>
                <div class="detail-row"><div class="d-label">Phone</div><div class="d-value muted">{{ $appointment->phone ?? '—' }}</div></div>
                <div class="detail-row"><div class="d-label">Email</div><div class="d-value muted">{{ $appointment->user?->email ?? '—' }}</div></div>
                @if($appointment->user)
                <div class="detail-row"><div class="d-label">User ID</div><div class="d-value muted">USER-{{ str_pad($appointment->user->id, 4, '0', STR_PAD_LEFT) }}</div></div>
                @endif
            </div>

            {{-- Appointment details --}}
            <div class="detail-panel">
                <p class="panel-title">Appointment Details</p>
                <div class="detail-row"><div class="d-label">Booking Status</div><div><span class="st-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span></div></div>
                @if($appointment->guru)
                <div class="detail-row"><div class="d-label">Guruji</div><div class="d-value">{{ $appointment->guru->name }}</div></div>
                @endif
                @if($appointment->notes)
                <div class="detail-row"><div class="d-label">Notes</div><div class="d-value muted">{{ $appointment->notes }}</div></div>
                @endif
                <div class="detail-row"><div class="d-label">Booking Ref</div><div class="d-value muted" style="font-family:ui-monospace,monospace">#ARK-{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}</div></div>
            </div>

            {{-- Payment details --}}
            <div class="detail-panel">
                <p class="panel-title">Payment Details</p>
                @php $ps = $appointment->payment_status ?? 'unpaid'; @endphp
                <div class="detail-row"><div class="d-label">Payment Status</div><div><span class="st-{{ $ps }}">{{ ucfirst(str_replace('_',' ',$ps)) }}</span></div></div>
                <div class="detail-row"><div class="d-label">Transaction ID</div><div class="d-value muted" style="font-family:ui-monospace,monospace;font-size:13px">{{ $appointment->payment_id ?: '—' }}</div></div>
                @if($appointment->payment_screenshot)
                <div class="detail-row">
                    <div class="d-label">Screenshot</div>
                    <a href="{{ Storage::disk('public')->url($appointment->payment_screenshot) }}" target="_blank">
                        <img src="{{ Storage::disk('public')->url($appointment->payment_screenshot) }}" style="max-height:160px;border-radius:6px;border:1px solid #d0e0ee;margin-top:4px;cursor:zoom-in">
                    </a>
                </div>
                @endif
            </div>

            {{-- Timestamps --}}
            <div class="detail-panel">
                <p class="panel-title">Timestamps</p>
                <div class="detail-row"><div class="d-label">Booked On</div><div class="d-value">{{ $appointment->created_at->format('d M Y, g:i A') }}</div></div>
                <div class="detail-row"><div class="d-label">Last Updated</div><div class="d-value muted">{{ $appointment->updated_at->format('d M Y, g:i A') }}</div></div>
                <div class="detail-row"><div class="d-label">Internal ID</div><div class="d-value muted" style="font-family:ui-monospace,monospace">#{{ $appointment->id }}</div></div>
            </div>

            {{-- Update Booking Status --}}
            @can('manage-appointments')
            @if(!in_array($appointment->status, ['completed','cancelled']))
            <div class="detail-panel">
                <p class="panel-title">Update Booking Status</p>
                @if(session('success'))<div style="margin-bottom:12px;padding:10px 14px;border-radius:8px;background:#d3f9d8;color:#1a4d2a;font-size:14px;font-weight:600">✓ {{ session('success') }}</div>@endif
                <form method="POST" action="{{ route('appointments.status', $appointment) }}">
                    @csrf @method('PATCH')
                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                        <div style="flex:1;min-width:160px">
                            <div class="d-label" style="margin-bottom:4px">Booking Status</div>
                            <select name="status" style="height:38px;width:100%;padding:0 10px;border:1px solid #c8d8e8;border-radius:8px;font:inherit">
                                @foreach(['pending','confirmed','completed','cancelled'] as $s)
                                <option value="{{ $s }}" @selected($appointment->status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" style="height:38px;padding:0 18px;border:0;border-radius:8px;background:#1a7ac0;color:#fff;font:700 14px inherit;cursor:pointer;white-space:nowrap">Save Booking Status</button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Update Payment Status --}}
            <div class="detail-panel" style="@if(in_array($appointment->status, ['completed','cancelled']))grid-column:1/-1@endif">
                <p class="panel-title">Update Payment Status</p>
                <form method="POST" action="{{ route('appointments.payment', $appointment) }}">
                    @csrf @method('PATCH')
                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                        <div style="flex:1;min-width:160px">
                            <div class="d-label" style="margin-bottom:4px">Payment Status</div>
                            <select name="payment_status" style="height:38px;width:100%;padding:0 10px;border:1px solid #c8d8e8;border-radius:8px;font:inherit">
                                @foreach(['unpaid','screenshot_uploaded','paid','cancelled'] as $ps)
                                <option value="{{ $ps }}" @selected(($appointment->payment_status ?? 'unpaid') === $ps)>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:2;min-width:200px">
                            <div class="d-label" style="margin-bottom:4px">Transaction ID</div>
                            <input type="text" name="payment_id" style="height:38px;width:100%;padding:0 10px;border:1px solid #c8d8e8;border-radius:8px;font:inherit" placeholder="Enter UTR / transaction ID" value="{{ old('payment_id', $appointment->payment_id) }}">
                        </div>
                        <button type="submit" style="height:38px;padding:0 18px;border:0;border-radius:8px;background:#1a7ac0;color:#fff;font:700 14px inherit;cursor:pointer;white-space:nowrap">Save Payment Status</button>
                    </div>
                </form>
            </div>
            @endcan

            {{-- Samagri list if present --}}
            @if($appointment->samagri && count($appointment->samagri))
            <div class="detail-panel" style="grid-column:1/-1">
                <p class="panel-title">Samagri / Materials</p>
                <table style="width:100%;border-collapse:collapse;font-size:14px">
                    <thead><tr style="border-bottom:2px solid #e8f0f5">
                        <th style="text-align:left;padding:6px 0;color:#6080a0;font-size:11px;text-transform:uppercase;letter-spacing:.04em">Item</th>
                        <th style="text-align:right;padding:6px 0;color:#6080a0;font-size:11px;text-transform:uppercase;letter-spacing:.04em">Qty</th>
                    </tr></thead>
                    <tbody>
                    @foreach($appointment->samagri as $item)
                    <tr style="border-bottom:1px solid #e8f0f5">
                        <td style="padding:8px 0">{{ $item['name'] ?? $item }}</td>
                        <td style="text-align:right;padding:8px 0">{{ $item['qty'] ?? 1 }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div style="margin-top:18px;display:flex;gap:10px">
            <a href="{{ route('appointments.index') }}" class="btn-back">← All Appointments</a>
        </div>
    </div>
</div>
@endsection
