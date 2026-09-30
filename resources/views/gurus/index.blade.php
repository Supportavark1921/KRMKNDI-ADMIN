@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
:root{--don:#e8813a;--don-lt:#fff4eb;--don-dk:#c4601a}
.don-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}
.don-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1200px;margin:32px auto 20px;padding:36px 44px;border-radius:22px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}
.don-hero h1{margin:8px 0;font-size:36px;color:#fff}
.don-hero p{max-width:580px;margin:0;color:#f8e4cc;line-height:1.6}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.hero-add-btn{display:flex;align-items:center;gap:8px;flex:0 0 auto;padding:13px 18px;border-radius:12px;color:#4a1f05;background:linear-gradient(120deg,#ffdd7c,#f0a030);font-size:14px;font-weight:800;white-space:nowrap;text-decoration:none;box-shadow:0 10px 22px #7a3a1033;transition:transform .15s}
.hero-add-btn:hover{transform:translateY(-2px)}
.don-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;max-width:1200px;margin:0 auto 18px}
.don-stat{display:flex;align-items:center;gap:12px;padding:17px 20px;border:1px solid #f0e8da;border-radius:15px;background:#fff}
.don-stat-icon{display:grid;width:38px;height:38px;place-items:center;border-radius:11px;font-size:18px}
.si-total{color:#c4601a;background:#fff4eb}
.si-active{color:#1e6b47;background:#e2f7ed}
.si-inactive{color:#7a4a18;background:#fff3e0}
.don-stat strong,.don-stat small{display:block}
.don-stat strong{color:#2a1810;font-size:20px}
.don-stat small{color:#9a8070;font-size:12px}
.don-toolbar{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;flex-wrap:wrap}
.don-search{flex:1;min-width:200px;position:relative}
.don-search input{padding:11px 14px 11px 40px;border:1px solid #e8ddd0;border-radius:11px;font:inherit;color:#2a1810;background:#fff;width:100%;outline:none;transition:.2s}
.don-search input:focus{border-color:#e8813a;box-shadow:0 0 0 3px #fff4eb}
.don-search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#c0977a;font-size:15px}
.don-board{max-width:1200px;margin:0 auto;border:1px solid #ede0d0;border-radius:20px;background:#fff;box-shadow:0 18px 45px #7a3a1008;overflow:hidden}
.don-table{width:100%;border-collapse:collapse}
.don-table thead th{padding:13px 16px;border-bottom:1px solid #f5ece0;background:#fffaf5;color:#8a7060;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;text-align:left}
.don-table thead th:last-child{text-align:right}
.don-table tbody tr{border-bottom:1px solid #f8f0e8;transition:background .15s}
.don-table tbody tr:last-child{border:0}
.don-table tbody tr:hover{background:#fffaf5}
.don-table td{padding:15px 16px;vertical-align:middle}
.guru-thumb{width:50px;height:50px;border-radius:50%;object-fit:cover;background:#fff4eb;border:2px solid #f0d0a8;display:flex;align-items:center;justify-content:center;color:#c4601a;font-size:20px;overflow:hidden;flex-shrink:0}
.guru-thumb img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.guru-name-cell{display:flex;align-items:center;gap:12px}
.guru-name b{display:block;font-size:15px;color:#2a1810;margin-bottom:3px}
.guru-name span{font-size:12px;color:#9a8070}
.cat-count{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:8px;background:#fff4eb;color:#c4601a;font-size:12px;font-weight:700}
.donate-total{font-size:15px;font-weight:800;color:#2a1810}
.status-active{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.status-inactive{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:11px;font-weight:750}
.status-active::before,.status-inactive::before{content:"";width:6px;height:6px;border-radius:50%}
.status-active::before{background:#28a76a}
.status-inactive::before{background:#e69c3a}
.action-cell{display:flex;align-items:center;justify-content:flex-end;gap:6px}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:1px solid #ede0d0;background:#fff;color:#7a6050;font-size:14px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#e8813a;color:#e8813a;background:#fff4eb}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.don-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:72px 24px;text-align:center}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.flash-error{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fff0f0;color:#b33;font-size:14px;font-weight:600}
.pag-wrap{display:flex;justify-content:center;padding:20px;border-top:1px solid #f5ece0}
.pag-wrap .pagination{display:flex;gap:6px;list-style:none;margin:0;padding:0}
.pag-wrap .page-item .page-link{display:flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;border:1px solid #ede0d0;border-radius:9px;color:#7a6050;font-size:13px;font-weight:600;text-decoration:none;transition:.15s}
.pag-wrap .page-item.active .page-link{background:#e8813a;border-color:#e8813a;color:#fff}
@media(max-width:700px){.don-stats{grid-template-columns:1fr}.don-hero{flex-direction:column;padding:28px}}
</style>

<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span>Donations › Gurujis</span><small>Manage Guruji profiles and donation categories</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error">⚠ {{ session('error') }}</div>@endif

    <div class="don-hero">
        <div>
            <span class="hero-overline">Admin · Donations</span>
            <h1>Guruji Management</h1>
            <p>Each Guruji can receive donations for different purposes. Add categories, view totals, and manage status.</p>
        </div>
        <a href="{{ route('gurus.create') }}" class="hero-add-btn">＋ Add Guruji</a>
    </div>

    @php
        $total    = \App\Models\Guru::count();
        $active   = \App\Models\Guru::where('status','active')->count();
        $inactive = \App\Models\Guru::where('status','inactive')->count();
    @endphp
    <div class="don-stats">
        <div class="don-stat"><div class="don-stat-icon si-total">🕉</div><div><strong>{{ $total }}</strong><small>Total Gurujis</small></div></div>
        <div class="don-stat"><div class="don-stat-icon si-active">●</div><div><strong>{{ $active }}</strong><small>Active</small></div></div>
        <div class="don-stat"><div class="don-stat-icon si-inactive">●</div><div><strong>{{ $inactive }}</strong><small>Inactive</small></div></div>
    </div>

    <form method="GET" action="{{ route('gurus.index') }}" class="don-toolbar">
        <div class="don-search">
            <span class="don-search-icon">⌕</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search Guruji…" autocomplete="off">
        </div>
        <button type="submit" style="width:auto;margin:0;padding:10px 16px;border-radius:10px;background:#e8813a;border:0;color:#fff;font:700 13px inherit;cursor:pointer">Search</button>
        @if(request('search'))<a href="{{ route('gurus.index') }}" style="padding:10px 14px;border-radius:10px;border:1px solid #ede0d0;color:#7a6050;font-size:13px;font-weight:600;text-decoration:none;background:#fff">Clear</a>@endif
    </form>

    <div class="don-board">
        @if($gurus->isEmpty())
            <div class="don-empty">
                <div style="font-size:48px;margin-bottom:12px">🕉</div>
                <h3 style="margin:0 0 8px;color:#2a1810">No Gurujis yet</h3>
                <p style="color:#9a8070;margin:0 0 20px;font-size:14px">Add your first Guruji to start managing donations.</p>
                <a href="{{ route('gurus.create') }}" style="padding:12px 18px;border-radius:11px;background:#e8813a;color:#fff;font:700 14px inherit;text-decoration:none">＋ Add Guruji</a>
            </div>
        @else
            <table class="don-table">
                <thead>
                    <tr>
                        <th style="width:60px">Photo</th>
                        <th>Guruji</th>
                        <th>Categories</th>
                        <th>Total Donations</th>
                        <th>Transactions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gurus as $guru)
                        <tr>
                            <td>
                                <div class="guru-thumb">
                                    @if($guru->image)
                                        <img src="{{ Storage::disk('public')->url($guru->image) }}" alt="">
                                    @else 🕉 @endif
                                </div>
                            </td>
                            <td>
                                <div class="guru-name-cell">
                                    <div class="guru-name">
                                        <b>{{ $guru->name }}</b>
                                        <span>{{ \Illuminate\Support\Str::limit($guru->description, 55) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="cat-count">{{ $guru->donationCategories()->count() }} categories</span></td>
                            <td><span class="donate-total">₹{{ number_format($guru->totalDonations(), 2) }}</span></td>
                            <td style="color:#6a5040;font-weight:700">{{ number_format($guru->total_transactions ?? 0) }}</td>
                            <td><span class="status-{{ $guru->status }}">{{ ucfirst($guru->status) }}</span></td>
                            <td>
                                <div class="action-cell">
                                    <a href="{{ route('gurus.show', $guru) }}" class="btn-icon" title="Dashboard">📊</a>
                                    <a href="{{ route('gurus.edit', $guru) }}" class="btn-icon" title="Edit">✎</a>
                                    <form method="POST" action="{{ route('gurus.destroy', $guru) }}" onsubmit="return confirm('Delete {{ $guru->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Delete">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($gurus->hasPages())
                <div class="pag-wrap">{{ $gurus->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
