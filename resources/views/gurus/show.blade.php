@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.don-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}
.guru-show-wrap{max-width:1100px;margin:0 auto}
.guru-show-hero{display:flex;align-items:center;gap:24px;max-width:1100px;margin:32px auto 20px;padding:32px 38px;border-radius:20px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}
.hero-avatar{width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #f0d0a8;background:#fff4eb;display:flex;align-items:center;justify-content:center;font-size:36px;flex-shrink:0;overflow:hidden}
.hero-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.guru-show-hero h1{margin:6px 0;font-size:28px;color:#fff}
.guru-show-hero p{margin:0;color:#f8e4cc;font-size:13px}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.g-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:18px}
.g-stat{padding:20px;border:1px solid #ede0d0;border-radius:15px;background:#fff}
.g-stat-label{font-size:11px;font-weight:800;color:#a08060;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.g-stat-value{font-size:24px;font-weight:900;color:#2a1810}
.g-stat-sub{font-size:12px;color:#9a8070;margin-top:4px}
.g-panel{padding:24px;border:1px solid #ede0d0;border-radius:16px;background:#fff;margin-bottom:16px}
.g-panel-title{font-size:13px;font-weight:800;color:#a08060;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px;padding-bottom:12px;border-bottom:1px solid #f5ece0}
.cat-row{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #f8f0e8}
.cat-row:last-child{border:0;padding-bottom:0}
.cat-name{font-size:14px;font-weight:700;color:#2a1810}
.cat-total{font-size:14px;font-weight:800;color:#e8813a}
.cat-count-sm{font-size:12px;color:#9a8070}
.status-active{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:12px;font-weight:750}
.status-inactive{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:12px;font-weight:750}
.btn-edit{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:10px;background:#e8813a;color:#fff;font:700 14px inherit;text-decoration:none;transition:.15s;margin-left:12px}
.btn-edit:hover{background:#c4601a}
.btn-back{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:10px;border:1px solid #ede0d0;color:#7a6050;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.btn-back:hover{background:#fffaf5}
@media(max-width:700px){.g-grid{grid-template-columns:1fr}}
</style>
<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span><a href="{{ route('gurus.index') }}" style="color:inherit;text-decoration:none">Gurujis</a> › Dashboard</span><small>{{ $guru->name }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="guru-show-wrap">
        <div class="guru-show-hero">
            <div class="hero-avatar">
                @if($guru->image)<img src="{{ Storage::disk('public')->url($guru->image) }}" alt="">@else 🕉 @endif
            </div>
            <div style="flex:1">
                <span class="hero-overline">Guruji Dashboard</span>
                <h1>{{ $guru->name }}</h1>
                <p>{{ $guru->description }}</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end">
                <span class="status-{{ $guru->status }}">{{ ucfirst($guru->status) }}</span>
                <a href="{{ route('gurus.edit', $guru) }}" class="btn-edit" style="margin:0">✎ Edit</a>
            </div>
        </div>

        {{-- Stats grid --}}
        <div class="g-grid">
            <div class="g-stat">
                <div class="g-stat-label">Total Donations</div>
                <div class="g-stat-value">₹{{ number_format($guru->totalDonations(), 2) }}</div>
                <div class="g-stat-sub">All time successful</div>
            </div>
            <div class="g-stat">
                <div class="g-stat-label">Total Donors</div>
                <div class="g-stat-value">{{ number_format($guru->totalDonors()) }}</div>
                <div class="g-stat-sub">Unique users</div>
            </div>
            <div class="g-stat">
                <div class="g-stat-label">Transactions</div>
                <div class="g-stat-value">{{ number_format($guru->totalTransactions()) }}</div>
                <div class="g-stat-sub">Successful payments</div>
            </div>
            <div class="g-stat">
                <div class="g-stat-label">This Month</div>
                <div class="g-stat-value">₹{{ number_format($guru->thisMonthDonations(), 2) }}</div>
                <div class="g-stat-sub">{{ now()->format('F Y') }}</div>
            </div>
            <div class="g-stat">
                <div class="g-stat-label">Handling Charges</div>
                <div class="g-stat-value">₹{{ number_format($guru->totalHandlingCharges(), 2) }}</div>
                <div class="g-stat-sub">App handling fees collected</div>
            </div>
            <div class="g-stat">
                <div class="g-stat-label">GST Collected</div>
                <div class="g-stat-value">₹{{ number_format($guru->totalGst(), 2) }}</div>
                <div class="g-stat-sub">GST on handling charges</div>
            </div>
        </div>

        {{-- Category breakdown --}}
        <div class="g-panel">
            <p class="g-panel-title">Category-wise Donation Breakdown</p>
            @if($categoryStats->isEmpty())
                <p style="color:#9a8070;font-size:14px;margin:0">No donations yet.</p>
            @else
                @foreach($categoryStats as $cat)
                    <div class="cat-row">
                        <div>
                            <div class="cat-name">{{ $cat['name'] }}</div>
                            <div class="cat-count-sm">{{ $cat['count'] }} donation{{ $cat['count'] !== 1 ? 's' : '' }}</div>
                        </div>
                        <div class="cat-total">₹{{ number_format($cat['total'], 2) }}</div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Assigned categories --}}
        <div class="g-panel">
            <p class="g-panel-title">Assigned Donation Categories ({{ $guru->donationCategories->count() }})</p>
            @if($guru->donationCategories->isEmpty())
                <p style="color:#9a8070;font-size:14px;margin:0">No categories assigned. <a href="{{ route('gurus.edit', $guru) }}" style="color:#e8813a;font-weight:700">Assign categories</a>.</p>
            @else
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($guru->donationCategories as $cat)
                        <span style="padding:7px 14px;border-radius:9px;background:#fff4eb;border:1px solid #f0d0a8;color:#c4601a;font-size:13px;font-weight:700">{{ $cat->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div style="display:flex;gap:10px;margin-top:4px">
            <a href="{{ route('gurus.index') }}" class="btn-back">← Back</a>
            <a href="{{ route('donations.index') }}?guru_id={{ $guru->id }}" class="btn-back">View Donations</a>
            <a href="{{ route('gurus.edit', $guru) }}" class="btn-edit">✎ Edit Guruji</a>
        </div>
    </div>
</div>
@endsection
