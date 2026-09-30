@extends('layouts.app')
@section('content')
<style>
.don-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}
.don-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1300px;margin:32px auto 20px;padding:34px 42px;border-radius:22px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}
.don-hero h1{margin:8px 0;font-size:34px;color:#fff}.don-hero p{max-width:580px;margin:0;color:#f8e4cc;line-height:1.6}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.d-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;max-width:1300px;margin:0 auto 16px}
.d-stat{padding:16px 18px;border:1px solid #f0e8da;border-radius:14px;background:#fff}
.d-stat-label{font-size:10px;font-weight:800;color:#a08060;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.d-stat-value{font-size:20px;font-weight:900;color:#2a1810}
.d-toolbar{display:flex;align-items:center;gap:8px;max-width:1300px;margin:0 auto 14px;flex-wrap:wrap}
.d-search{flex:1;min-width:180px;position:relative}
.d-search input{padding:10px 13px 10px 38px;border:1px solid #e8ddd0;border-radius:10px;font:inherit;color:#2a1810;background:#fff;width:100%;outline:none;transition:.2s}
.d-search input:focus{border-color:#e8813a;box-shadow:0 0 0 3px #fff4eb}
.d-search-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#c0977a;font-size:14px}
.d-filter select,.d-filter input{padding:9px 11px;border:1px solid #e8ddd0;border-radius:10px;font:inherit;color:#3a2010;background:#fff;outline:none}
.d-filter select:focus,.d-filter input:focus{border-color:#e8813a}
.d-board{max-width:1300px;margin:0 auto;border:1px solid #ede0d0;border-radius:20px;background:#fff;box-shadow:0 18px 45px #7a3a1008;overflow:auto}
.d-table{width:100%;border-collapse:collapse;min-width:900px}
.d-table thead th{padding:12px 14px;border-bottom:1px solid #f5ece0;background:#fffaf5;color:#8a7060;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;text-align:left;white-space:nowrap}
.d-table tbody tr{border-bottom:1px solid #f8f0e8;transition:background .15s}
.d-table tbody tr:last-child{border:0}
.d-table tbody tr:hover{background:#fffaf5}
.d-table td{padding:13px 14px;vertical-align:middle;font-size:13px}
.don-id{font-family:ui-monospace,monospace;font-size:12px;font-weight:700;color:#6a4030}
.user-cell b{display:block;font-size:13px;color:#2a1810}
.user-cell small{font-size:11px;color:#9a8070}
.amount-cell{font-weight:800;color:#2a1810}
.charge-cell{color:#9a8070}
.total-cell{font-weight:800;color:#e8813a}
.st-success{display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:10px;font-weight:750}
.st-pending{display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:10px;font-weight:750}
.st-failed{display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:20px;color:#8b3030;background:#fdeaea;font-size:10px;font-weight:750}
.st-cancelled{display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:20px;color:#606070;background:#f0f0f5;font-size:10px;font-weight:750}
.st-refunded{display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:20px;color:#1a4a80;background:#e3eeff;font-size:10px;font-weight:750}
.st-success::before,.st-pending::before,.st-failed::before,.st-cancelled::before,.st-refunded::before{content:"";width:5px;height:5px;border-radius:50%}
.st-success::before{background:#28a76a}.st-pending::before{background:#e69c3a}.st-failed::before{background:#e04a4a}.st-cancelled::before{background:#909090}.st-refunded::before{background:#1a6abf}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;border:1px solid #ede0d0;background:#fff;color:#7a6050;font-size:12px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#e8813a;color:#e8813a;background:#fff4eb}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1300px;margin:0 auto 14px;padding:12px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.pag-wrap{display:flex;justify-content:center;padding:18px;border-top:1px solid #f5ece0}
.pag-wrap .pagination{display:flex;gap:5px;list-style:none;margin:0;padding:0}
.pag-wrap .page-item .page-link{display:flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 9px;border:1px solid #ede0d0;border-radius:8px;color:#7a6050;font-size:12px;font-weight:600;text-decoration:none;transition:.15s}
.pag-wrap .page-item.active .page-link{background:#e8813a;border-color:#e8813a;color:#fff}
@media(max-width:800px){.d-stats{grid-template-columns:1fr 1fr}}
</style>
<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span>Donations</span><small>All donation transactions</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

    <div class="don-hero">
        <div><span class="hero-overline">Admin · Donations</span><h1>Donation Management</h1><p>View and filter all donation transactions across Gurujis and categories.</p></div>
    </div>

    <div class="d-stats">
        <div class="d-stat"><div class="d-stat-label">Total Donated</div><div class="d-stat-value">₹{{ number_format($stats['total'], 2) }}</div></div>
        <div class="d-stat"><div class="d-stat-label">Successful</div><div class="d-stat-value">{{ number_format($stats['successful']) }}</div></div>
        <div class="d-stat"><div class="d-stat-label">Unique Donors</div><div class="d-stat-value">{{ number_format($stats['donors']) }}</div></div>
        <div class="d-stat"><div class="d-stat-label">Handling Charges</div><div class="d-stat-value">₹{{ number_format($stats['handling'], 2) }}</div></div>
        <div class="d-stat"><div class="d-stat-label">GST Collected</div><div class="d-stat-value">₹{{ number_format($stats['gst'], 2) }}</div></div>
    </div>

    <form method="GET" action="{{ route('donations.index') }}" class="d-toolbar">
        <div class="d-search">
            <span class="d-search-icon">⌕</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="DON-ID, TXN, User name…" autocomplete="off">
        </div>
        <div class="d-filter">
            <select name="guru_id">
                <option value="">All Gurujis</option>
                @foreach($gurus as $g)<option value="{{ $g->id }}" @selected(request('guru_id') == $g->id)>{{ $g->name }}</option>@endforeach
            </select>
        </div>
        <div class="d-filter">
            <select name="category_id">
                <option value="">All Categories</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div class="d-filter">
            <select name="payment_status">
                <option value="">All Status</option>
                @foreach($statuses as $s)<option value="{{ $s }}" @selected(request('payment_status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
        </div>
        <div class="d-filter"><input type="date" name="date_from" value="{{ request('date_from') }}" title="From date"></div>
        <div class="d-filter"><input type="date" name="date_to" value="{{ request('date_to') }}" title="To date"></div>
        <button type="submit" style="width:auto;margin:0;padding:9px 14px;border-radius:10px;background:#e8813a;border:0;color:#fff;font:700 13px inherit;cursor:pointer">Filter</button>
        @if(request()->hasAny(['search','guru_id','category_id','payment_status','date_from','date_to']))
            <a href="{{ route('donations.index') }}" style="padding:9px 12px;border-radius:10px;border:1px solid #ede0d0;color:#7a6050;font-size:13px;font-weight:600;text-decoration:none;background:#fff">Clear</a>
        @endif
    </form>

    <div class="d-board">
        @if($donations->isEmpty())
            <div style="padding:60px;text-align:center">
                <div style="font-size:36px;margin-bottom:12px">₹</div>
                <h3 style="margin:0 0 8px;color:#2a1810">No donations found</h3>
                <p style="color:#9a8070;margin:0;font-size:14px">Try adjusting your filters.</p>
            </div>
        @else
            <table class="d-table">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>User</th>
                        <th>Guruji</th>
                        <th>Purpose</th>
                        <th>Donation</th>
                        <th>Handling</th>
                        <th>GST</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donations as $d)
                        <tr>
                            <td><span class="don-id">{{ $d->donation_id }}</span></td>
                            <td>
                                <div class="user-cell">
                                    <b>{{ $d->user->name ?? '—' }}</b>
                                    <small>{{ $d->user?->email }}</small>
                                </div>
                            </td>
                            <td style="font-size:13px;font-weight:700;color:#4a3020">{{ $d->guru->name ?? '—' }}</td>
                            <td style="font-size:13px;color:#6a5040">{{ $d->category->name ?? '—' }}</td>
                            <td class="amount-cell">₹{{ number_format($d->donation_amount, 2) }}</td>
                            <td class="charge-cell">₹{{ number_format($d->handling_charge, 2) }}</td>
                            <td class="charge-cell">₹{{ number_format($d->gst_amount, 2) }}</td>
                            <td class="total-cell">₹{{ number_format($d->total_amount, 2) }}</td>
                            <td><span class="st-{{ $d->payment_status }}">{{ ucfirst($d->payment_status) }}</span></td>
                            <td style="color:#9a8070;white-space:nowrap;font-size:12px">{{ $d->created_at->format('d M Y') }}</td>
                            <td><a href="{{ route('donations.show', $d) }}" class="btn-icon" title="View">👁</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($donations->hasPages())
                <div class="pag-wrap">{{ $donations->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
