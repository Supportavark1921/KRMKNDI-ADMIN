@extends('layouts.app')
@use('Illuminate\Support\Str')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
:root{--sv:#6246ea;--sv-lt:#ede9ff;--sv-dk:#4934c4;}
.svc-page{background:radial-gradient(circle at 90% 4%,#e6e0ff 0,transparent 22%),#f6f7fc!important}
.svc-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1200px;margin:35px auto 22px;padding:38px 44px;border-radius:22px;color:#fff;background:radial-gradient(circle at 86% 10%,#9d80ef 0,transparent 26%),linear-gradient(118deg,#1a1442,#3a2d82)}
.svc-hero h1{margin:8px 0;font-size:38px;color:#fff}
.svc-hero p{max-width:600px;margin:0;color:#d8d4f8;line-height:1.6}
.hero-overline{color:#ffdc87;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.hero-add-btn{display:flex;align-items:center;gap:8px;flex:0 0 auto;padding:13px 18px;border-radius:12px;color:#2c1d6b;background:linear-gradient(120deg,#ffdd7c,#f0ab4d);font-size:14px;font-weight:800;white-space:nowrap;text-decoration:none;box-shadow:0 10px 22px #08142f26;transition:transform .15s}
.hero-add-btn:hover{transform:translateY(-2px)}
.svc-toolbar{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 18px;flex-wrap:wrap}
.svc-search{flex:1;min-width:200px;position:relative}
.svc-search input{padding:11px 14px 11px 40px;border:1px solid #e0e3ef;border-radius:11px;font:inherit;color:#1e2640;background:#fff;width:100%;outline:none;transition:.2s}
.svc-search input:focus{border-color:#6246ea;box-shadow:0 0 0 3px #ede9ff}
.svc-search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9aa3bc;font-size:15px}
.svc-filter select{padding:10px 12px;border:1px solid #e0e3ef;border-radius:11px;font:inherit;color:#3a4060;background:#fff;outline:none;cursor:pointer}
.svc-filter select:focus{border-color:#6246ea}
.svc-board{max-width:1200px;margin:0 auto;border:1px solid #e4e7f2;border-radius:20px;background:#fff;box-shadow:0 18px 45px #1e2a5a09;overflow:hidden}
.svc-table{width:100%;border-collapse:collapse}
.svc-table thead th{padding:13px 16px;border-bottom:1px solid #eef0f7;background:#fafbff;color:#6b748c;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;text-align:left}
.svc-table thead th:last-child{text-align:right}
.svc-table tbody tr{border-bottom:1px solid #f0f2f8;transition:background .15s}
.svc-table tbody tr:last-child{border:0}
.svc-table tbody tr:hover{background:#faf9ff}
.svc-table td{padding:15px 16px;vertical-align:middle}
.svc-thumb{width:54px;height:54px;border-radius:12px;object-fit:cover;background:#f0ecff;border:1px solid #e6e3f7;display:flex;align-items:center;justify-content:center;color:#7b68cc;font-size:22px;overflow:hidden;flex-shrink:0}
.svc-thumb img{width:100%;height:100%;object-fit:cover}
.svc-name-cell{display:flex;align-items:center;gap:14px}
.svc-name b{display:block;font-size:15px;color:#1b2240;margin-bottom:3px}
.svc-name span{font-size:12px;color:#7882a0}
.lang-chip{display:inline-block;padding:3px 7px;border-radius:6px;background:#ede9ff;color:#5a42a8;font-size:10px;font-weight:800;letter-spacing:.04em;margin:2px 2px 2px 0;text-transform:uppercase}
.price-cell{font-size:15px;font-weight:800;color:#1b2240}
.price-cell small{display:block;font-size:11px;color:#9aa3bc;font-weight:400;text-decoration:line-through}
.status-active{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.status-inactive{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:11px;font-weight:750}
.status-active::before,.status-inactive::before{content:"";width:6px;height:6px;border-radius:50%}
.status-active::before{background:#28a76a}
.status-inactive::before{background:#e69c3a}
.action-cell{display:flex;align-items:center;justify-content:flex-end;gap:6px}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font-size:14px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#6246ea;color:#6246ea;background:#ede9ff}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.svc-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:72px 24px;text-align:center}
.svc-empty-icon{display:grid;width:60px;height:60px;place-items:center;border-radius:18px;background:#ede9ff;color:#6246ea;font-size:30px;margin:0 auto 16px}
.svc-empty h3{margin:0 0 8px;color:#1b2240;font-size:20px}
.svc-empty p{margin:0 0 20px;color:#7882a0;font-size:14px}
.svc-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;max-width:1200px;margin:0 auto 18px}
.svc-stat{display:flex;align-items:center;gap:12px;padding:17px 20px;border:1px solid #e6e8f0;border-radius:15px;background:#fff}
.stat-icon{display:grid;width:38px;height:38px;place-items:center;border-radius:11px;font-size:18px}
.stat-icon.all{color:#5b49aa;background:#ede9ff}
.stat-icon.active{color:#1e6b47;background:#e2f7ed}
.stat-icon.inactive{color:#7a4a18;background:#fff3e0}
.svc-stat strong,.svc-stat small{display:block}
.svc-stat strong{color:#1b2240;font-size:20px}
.svc-stat small{color:#7882a0;font-size:12px}
.pagination-wrap{display:flex;justify-content:center;padding:20px;border-top:1px solid #f0f2f8}
.pagination-wrap .pagination{display:flex;gap:6px;list-style:none;margin:0;padding:0}
.pagination-wrap .page-item .page-link{display:flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;border:1px solid #e4e7f2;border-radius:9px;color:#555e7a;font-size:13px;font-weight:600;text-decoration:none;transition:.15s}
.pagination-wrap .page-item.active .page-link{background:#6246ea;border-color:#6246ea;color:#fff}
.pagination-wrap .page-item .page-link:hover:not(.active){background:#f0ecff;border-color:#c4b7f5;color:#6246ea}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
</style>

<div class="dashboard svc-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small>Service Management</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))
        <div class="flash-success">✓ {{ session('success') }}</div>
    @endif

    {{-- Hero --}}
    <div class="svc-hero">
        <div>
            <span class="hero-overline">Admin · Services</span>
            <h1>Service Management</h1>
            <p>Create and manage multilingual services. Content is served to the mobile app in the requested language.</p>
        </div>
        <a href="{{ route('services.create') }}" class="hero-add-btn">＋ Add Service</a>
    </div>

    {{-- Stats --}}
    @php
        $total    = $services->total();
        $active   = \App\Models\Service::where('status','active')->count();
        $inactive = \App\Models\Service::where('status','inactive')->count();
    @endphp
    <div class="svc-stats">
        <div class="svc-stat"><div class="stat-icon all">✦</div><div><strong>{{ $total }}</strong><small>Total Services</small></div></div>
        <div class="svc-stat"><div class="stat-icon active">●</div><div><strong>{{ $active }}</strong><small>Active</small></div></div>
        <div class="svc-stat"><div class="stat-icon inactive">●</div><div><strong>{{ $inactive }}</strong><small>Inactive</small></div></div>
    </div>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('services.index') }}" class="svc-toolbar">
        <div class="svc-search">
            <span class="svc-search-icon">⌕</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search services…" autocomplete="off">
        </div>
        <div class="svc-filter">
            <select name="language" onchange="this.form.submit()">
                <option value="">All Languages</option>
                @foreach($languages as $code => $label)
                    <option value="{{ $code }}" @selected(request('language') === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="svc-filter">
            <select name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active"   @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </div>
        <button type="submit" style="width:auto;margin:0;padding:10px 16px;border-radius:10px;background:var(--violet);box-shadow:none;font-size:13px">Search</button>
        @if(request()->hasAny(['search','language','status']))
            <a href="{{ route('services.index') }}" style="padding:10px 14px;border-radius:10px;border:1px solid #e0e3ef;color:#555e7a;font-size:13px;font-weight:600;text-decoration:none;background:#fff">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="svc-board">
        @if($services->isEmpty())
            <div class="svc-empty">
                <div class="svc-empty-icon">✦</div>
                <h3>No services yet</h3>
                <p>Add your first multilingual service to get started.</p>
                <a href="{{ route('services.create') }}" class="primary-link" style="margin:0;padding:12px 18px;border-radius:11px;text-decoration:none">＋ Add Service</a>
            </div>
        @else
            <table class="svc-table">
                <thead>
                    <tr>
                        <th style="width:70px">Image</th>
                        <th>Service</th>
                        <th>Languages</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td>
                                <div class="svc-thumb">
                                    @if($service->primaryImage())
                                        <img src="{{ Storage::disk('public')->url($service->primaryImage()) }}" alt="">
                                    @else
                                        ✦
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="svc-name-cell">
                                    <div class="svc-name">
                                        <b>{{ $service->name() }}</b>
                                        <span>{{ Str::limit($service->title(), 50) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($service->activeLanguages() as $lang)
                                    <span class="lang-chip">{{ $lang }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="price-cell">
                                    @if($service->amount() !== null)
                                        {{ $service->currency() === 'INR' ? '₹' : $service->currency() }}{{ number_format($service->amount()) }}
                                        @if($service->discountAmount() !== null)
                                            <small>{{ $service->currency() === 'INR' ? '₹' : $service->currency() }}{{ number_format($service->discountAmount()) }}</small>
                                        @endif
                                    @else
                                        <span style="color:#9aa3bc">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="status-{{ $service->status }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td style="color:#9aa3bc;font-size:12px">{{ $service->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="action-cell">
                                    <a href="{{ route('services.show', $service) }}" class="btn-icon" title="View">👁</a>
                                    <a href="{{ route('services.edit', $service) }}" class="btn-icon" title="Edit">✎</a>
                                    <form method="POST" action="{{ route('services.destroy', $service) }}" onsubmit="return confirm('Delete this service? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Delete" style="border:1px solid #e4e7f2">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($services->hasPages())
                <div class="pagination-wrap">
                    {{ $services->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
