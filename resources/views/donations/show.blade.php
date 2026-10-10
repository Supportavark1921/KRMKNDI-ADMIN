@extends('layouts.app')
@section('content')
<style>
.don-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}
.don-detail-wrap{max-width:860px;margin:0 auto}
.don-detail-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin:32px auto 20px;padding:30px 36px;border-radius:20px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}
.don-detail-hero h1{margin:6px 0;font-size:26px;color:#fff}
.don-detail-hero p{margin:0;color:#f8e4cc;font-size:13px}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.detail-panel{padding:22px 26px;border:1px solid #ede0d0;border-radius:16px;background:#fff;box-shadow:0 4px 12px #7a3a1006}
.panel-title{font-size:11px;font-weight:800;color:#a08060;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px;padding-bottom:11px;border-bottom:1px solid #f5ece0}
.detail-row{margin-bottom:14px}
.detail-row:last-child{margin-bottom:0}
.d-label{font-size:11px;font-weight:700;color:#b0907a;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px}
.d-value{font-size:15px;font-weight:700;color:#2a1810}
.d-value.muted{font-weight:400;color:#6a5040;font-size:14px}
.breakdown-panel{padding:22px 26px;border:1px solid #ede0d0;border-radius:16px;background:#fff;margin-bottom:16px}
.breakdown-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f5ece0;font-size:14px}
.breakdown-row:last-child{border-bottom:0}
.breakdown-row .bl{color:#6a5040}
.breakdown-row .br{font-weight:700;color:#2a1810}
.breakdown-total{display:flex;justify-content:space-between;align-items:center;padding:14px 0 0;margin-top:4px;border-top:2px solid #e8ddd0;font-size:16px;font-weight:900}
.breakdown-total .bl{color:#2a1810}
.breakdown-total .br{color:#e8813a;font-size:20px}
.don-id-big{font-family:ui-monospace,monospace;font-size:24px;font-weight:900;color:#e8813a;letter-spacing:.04em}
.st-success{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:13px;font-weight:750}
.st-pending{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:13px;font-weight:750}
.st-failed{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#8b3030;background:#fdeaea;font-size:13px;font-weight:750}
.st-cancelled{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#606070;background:#f0f0f5;font-size:13px;font-weight:750}
.st-refunded{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;color:#1a4a80;background:#e3eeff;font-size:13px;font-weight:750}
.st-success::before,.st-pending::before,.st-failed::before,.st-cancelled::before,.st-refunded::before{content:"";width:7px;height:7px;border-radius:50%}
.st-success::before{background:#28a76a}.st-pending::before{background:#e69c3a}.st-failed::before{background:#e04a4a}.st-cancelled::before{background:#909090}.st-refunded::before{background:#1a6abf}
.btn-back{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:10px;border:1px solid #ede0d0;color:#7a6050;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.btn-back:hover{background:#fffaf5}
@media(max-width:650px){.detail-grid{grid-template-columns:1fr}}
</style>
<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span><a href="{{ route('donations.index') }}" style="color:inherit;text-decoration:none">Donations</a> › Detail</span><small>{{ $donation->donation_id }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="don-detail-wrap">
        <div class="don-detail-hero">
            <div>
                <span class="hero-overline">Donation Detail</span>
                <h1>{{ $donation->donation_id }}</h1>
                <p>{{ $donation->created_at->format('d M Y, g:i A') }} · {{ $donation->guru->name ?? '—' }} · {{ $donation->category->name ?? '—' }}</p>
            </div>
            <span class="st-{{ $donation->payment_status }}">{{ ucfirst($donation->payment_status) }}</span>
        </div>

        {{-- Payment breakdown (full width) --}}
        <div class="breakdown-panel">
            <p class="panel-title" style="margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f5ece0">Payment Breakdown</p>
            <div class="breakdown-row"><span class="bl">Donation Amount</span><span class="br">₹{{ number_format($donation->donation_amount, 2) }}</span></div>
            <div class="breakdown-row"><span class="bl">App Handling Charge</span><span class="br">₹{{ number_format($donation->handling_charge, 2) }}</span></div>
            <div class="breakdown-row"><span class="bl">GST on Handling Charge ({{ $donation->gst_rate }}%)</span><span class="br">₹{{ number_format($donation->gst_amount, 2) }}</span></div>
            <div class="breakdown-total"><span class="bl">Total Paid</span><span class="br">₹{{ number_format($donation->total_amount, 2) }}</span></div>
        </div>

        <div class="detail-grid">
            {{-- User --}}
            <div class="detail-panel">
                <p class="panel-title">User Information</p>
                <div class="detail-row"><div class="d-label">Name</div><div class="d-value">{{ $donation->user->name ?? '—' }}</div></div>
                <div class="detail-row"><div class="d-label">Email</div><div class="d-value muted">{{ $donation->user->email ?? '—' }}</div></div>
                <div class="detail-row"><div class="d-label">User ID</div><div class="d-value muted">{{ $donation->user ? 'USER-' . str_pad($donation->user->id, 4, '0', STR_PAD_LEFT) : '—' }}</div></div>
            </div>

            {{-- Guruji & Purpose --}}
            <div class="detail-panel">
                <p class="panel-title">Guruji & Purpose</p>
                <div class="detail-row"><div class="d-label">Guruji</div><div class="d-value">{{ $donation->guru->name ?? '—' }}</div></div>
                <div class="detail-row"><div class="d-label">Donation Purpose</div><div class="d-value">{{ $donation->category->name ?? '—' }}</div></div>
                <div class="detail-row"><div class="d-label">Currency</div><div class="d-value muted">{{ $donation->currency }}</div></div>
            </div>

            {{-- Payment details --}}
            <div class="detail-panel">
                <p class="panel-title">Payment Details</p>
                <div class="detail-row"><div class="d-label">Status</div><div><span class="st-{{ $donation->payment_status }}">{{ ucfirst(str_replace('_',' ',$donation->payment_status)) }}</span></div></div>
                <div class="detail-row"><div class="d-label">Payment Method</div><div class="d-value muted">{{ $donation->payment_method ?: '—' }}</div></div>
                <div class="detail-row"><div class="d-label">Transaction ID</div><div class="d-value muted" style="font-family:ui-monospace,monospace;font-size:13px">{{ $donation->transaction_id ?: '—' }}</div></div>
                <div class="detail-row"><div class="d-label">Payment ID</div><div class="d-value muted" style="font-family:ui-monospace,monospace;font-size:13px">{{ $donation->payment_id ?: '—' }}</div></div>
                @if($donation->payment_screenshot)
                <div class="detail-row">
                    <div class="d-label">Screenshot</div>
                    <a href="{{ Storage::disk('public')->url($donation->payment_screenshot) }}" target="_blank">
                        <img src="{{ Storage::disk('public')->url($donation->payment_screenshot) }}" style="max-height:160px;border-radius:6px;border:1px solid #e8d8c8;margin-top:4px;cursor:zoom-in">
                    </a>
                </div>
                @endif
            </div>

            {{-- Admin payment update --}}
            <div class="detail-panel" style="grid-column:1/-1">
                <p class="panel-title">Update Payment Status</p>
                @if(session('success'))<div style="margin-bottom:12px;padding:10px 14px;border-radius:8px;background:#d3f9d8;color:#1a4d2a;font-size:14px;font-weight:600">✓ {{ session('success') }}</div>@endif
                <form method="POST" action="{{ route('donations.payment', $donation) }}">
                    @csrf @method('PATCH')
                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                        <div style="flex:1;min-width:160px">
                            <div class="d-label" style="margin-bottom:4px">Payment Status</div>
                            <select name="payment_status" style="height:38px;width:100%;padding:0 10px;border:1px solid #d8c8b8;border-radius:8px;font:inherit">
                                @foreach(['pending','screenshot_uploaded','success','failed','cancelled','refunded'] as $ps)
                                <option value="{{ $ps }}" @selected($donation->payment_status === $ps)>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:2;min-width:200px">
                            <div class="d-label" style="margin-bottom:4px">Transaction ID</div>
                            <input type="text" name="transaction_id" style="height:38px;width:100%;padding:0 10px;border:1px solid #d8c8b8;border-radius:8px;font:inherit" placeholder="Enter UTR / transaction ID" value="{{ old('transaction_id', $donation->transaction_id) }}">
                        </div>
                        <button type="submit" style="height:38px;padding:0 18px;border:0;border-radius:8px;background:#e8813a;color:#fff;font:700 14px inherit;cursor:pointer;white-space:nowrap">Save Payment Status</button>
                    </div>
                </form>
            </div>

            {{-- Timestamps --}}
            <div class="detail-panel">
                <p class="panel-title">Timestamps</p>
                <div class="detail-row"><div class="d-label">Donation Date</div><div class="d-value">{{ $donation->created_at->format('d M Y, g:i A') }}</div></div>
                <div class="detail-row"><div class="d-label">Last Updated</div><div class="d-value muted">{{ $donation->updated_at->format('d M Y, g:i A') }}</div></div>
                <div class="detail-row"><div class="d-label">Internal ID</div><div class="d-value muted" style="font-family:ui-monospace,monospace">#{{ $donation->id }}</div></div>
            </div>
        </div>

        <div style="margin-top:18px;display:flex;gap:10px">
            <a href="{{ route('donations.index') }}" class="btn-back">← All Donations</a>
            @if($donation->guru)
                <a href="{{ route('gurus.show', $donation->guru) }}" class="btn-back">Guruji Dashboard</a>
            @endif
        </div>
    </div>
</div>
@endsection
