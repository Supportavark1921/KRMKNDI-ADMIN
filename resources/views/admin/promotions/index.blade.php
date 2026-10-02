@extends('layouts.app')
@section('title', 'App Content — Promotions')
@section('content')
<style>
.prm-page{background:radial-gradient(circle at 92% 4%,#ffe8f5 0,transparent 22%),#fdf5fb!important}
.prm-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1200px;margin:35px auto 22px;padding:38px 44px;border-radius:22px;color:#fff;background:radial-gradient(circle at 86% 10%,#f783ac 0,transparent 26%),linear-gradient(118deg,#3d0a2e,#8a1f5e)}
.prm-hero h1{margin:8px 0;font-size:38px;color:#fff}
.prm-hero p{max-width:600px;margin:0;color:#f8cfe6;line-height:1.6}
.prm-overline{color:#ffd787;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.prm-add-btn{display:flex;align-items:center;gap:8px;flex:0 0 auto;padding:13px 18px;border-radius:12px;color:#3d0a2e;background:linear-gradient(120deg,#ffdd7c,#f0ab4d);font-size:14px;font-weight:800;white-space:nowrap;text-decoration:none;box-shadow:0 10px 22px #08142f26;transition:transform .15s}
.prm-add-btn:hover{transform:translateY(-2px)}
.prm-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;max-width:1200px;margin:0 auto 18px}
.prm-stat{display:flex;align-items:center;gap:12px;padding:17px 20px;border:1px solid #f0d4e8;border-radius:15px;background:#fff}
.prm-stat-icon{display:grid;width:38px;height:38px;place-items:center;border-radius:11px;font-size:18px}
.prm-stat-icon.all{color:#8a1f5e;background:#ffe8f5}
.prm-stat-icon.active{color:#1e6b47;background:#e2f7ed}
.prm-stat-icon.draft{color:#7a5a10;background:#fff3d2}
.prm-stat-icon.inactive{color:#666;background:#eee}
.prm-stat strong,.prm-stat small{display:block}
.prm-stat strong{color:#1b2240;font-size:20px}
.prm-stat small{color:#7882a0;font-size:12px}
.prm-toolbar{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 18px;flex-wrap:wrap}
.prm-search{flex:1;min-width:180px;position:relative}
.prm-search input{padding:11px 14px 11px 40px;border:1px solid #e0d0ec;border-radius:11px;font:inherit;color:#1e2640;background:#fff;width:100%;outline:none;transition:.2s}
.prm-search input:focus{border-color:#8a1f5e;box-shadow:0 0 0 3px #ffe8f5}
.prm-search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9aa3bc;font-size:15px}
.prm-filter select{padding:10px 12px;border:1px solid #e0d0ec;border-radius:11px;font:inherit;color:#3a4060;background:#fff;outline:none;cursor:pointer}
.prm-filter select:focus{border-color:#8a1f5e}
.prm-board{max-width:1200px;margin:0 auto;border:1px solid #f0d4e8;border-radius:20px;background:#fff;box-shadow:0 18px 45px #3d0a2e09;overflow:hidden}
.prm-table{width:100%;border-collapse:collapse}
.prm-table thead th{padding:13px 16px;border-bottom:1px solid #fae8f4;background:#fdf8fb;color:#7a3060;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;text-align:left}
.prm-table thead th:last-child{text-align:right}
.prm-table tbody tr{border-bottom:1px solid #fdf0f8;transition:background .15s}
.prm-table tbody tr:last-child{border:0}
.prm-table tbody tr:hover{background:#fdf8fb}
.prm-table td{padding:14px 16px;vertical-align:middle}
.prm-thumb{width:56px;height:40px;border-radius:8px;object-fit:cover;background:#fae8f4;border:1px solid #f0d4e8;display:flex;align-items:center;justify-content:center;color:#b04a8a;font-size:20px;overflow:hidden;flex-shrink:0}
.prm-thumb img{width:100%;height:100%;object-fit:cover}
.prm-title b{display:block;font-size:14px;color:#1b2240;margin-bottom:2px}
.prm-title span{font-size:11px;color:#9a7a90}
.prm-chip{display:inline-block;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:750}
.prm-type-banner{color:#1e4d8c;background:#dbeafe}
.prm-type-promotion{color:#8a1f5e;background:#ffe8f5}
.prm-type-announcement{color:#1a6b47;background:#e2f7ed}
.prm-type-offer{color:#7a5a10;background:#fff3d2}
.prm-status-active{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.prm-status-draft{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#7a5a10;background:#fff3d2;font-size:11px;font-weight:750}
.prm-status-inactive,.prm-status-archived{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#666;background:#eee;font-size:11px;font-weight:750}
.prm-status-active::before,.prm-status-draft::before,.prm-status-inactive::before,.prm-status-archived::before{content:"";width:6px;height:6px;border-radius:50%}
.prm-status-active::before{background:#28a76a}
.prm-status-draft::before{background:#f0a42e}
.prm-status-inactive::before,.prm-status-archived::before{background:#aaa}
.action-cell{display:flex;align-items:center;justify-content:flex-end;gap:6px}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font-size:14px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#8a1f5e;color:#8a1f5e;background:#ffe8f5}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.btn-icon.success:hover{border-color:#28a76a;color:#28a76a;background:#e2f7ed}
.btn-icon.warning:hover{border-color:#f0a42e;color:#a06020;background:#fff3d2}
.prm-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:72px 24px;text-align:center}
.prm-empty-icon{display:grid;width:60px;height:60px;place-items:center;border-radius:18px;background:#ffe8f5;color:#8a1f5e;font-size:30px;margin:0 auto 16px}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.flash-error{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fce8e8;color:#962a2a;font-size:14px;font-weight:600}
.pagination-wrap{display:flex;justify-content:center;padding:20px;border-top:1px solid #fae8f4}
</style>

<div class="dashboard prm-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small>App Content</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error">✗ {{ session('error') }}</div>@endif

    <div class="prm-hero">
        <div>
            <span class="prm-overline">App Content · Promotions</span>
            <h1>Promotions &amp; Banners</h1>
            <p>Manage banners, offers and announcements shown in the mobile app. Control placement, scheduling and audience.</p>
        </div>
        @can('promotions.create')
        <a href="{{ route('admin.promotions.create') }}" class="prm-add-btn">＋ New Promotion</a>
        @endcan
    </div>

    @php
        use App\Models\Promotion;
        $totalPromo    = Promotion::count();
        $activePromo   = Promotion::where('status','active')->count();
        $draftPromo    = Promotion::where('status','draft')->count();
        $inactivePromo = Promotion::where('status','inactive')->count();
    @endphp
    <div class="prm-stats">
        <div class="prm-stat"><div class="prm-stat-icon all">📣</div><div><strong>{{ $totalPromo }}</strong><small>Total</small></div></div>
        <div class="prm-stat"><div class="prm-stat-icon active">●</div><div><strong>{{ $activePromo }}</strong><small>Active</small></div></div>
        <div class="prm-stat"><div class="prm-stat-icon draft">●</div><div><strong>{{ $draftPromo }}</strong><small>Draft</small></div></div>
        <div class="prm-stat"><div class="prm-stat-icon inactive">●</div><div><strong>{{ $inactivePromo }}</strong><small>Inactive</small></div></div>
    </div>

    <form method="GET" action="{{ route('admin.promotions.index') }}" class="prm-toolbar">
        <div class="prm-search">
            <span class="prm-search-icon">⌕</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search title…" autocomplete="off">
        </div>
        <div class="prm-filter">
            <select name="type" onchange="this.form.submit()">
                <option value="">All types</option>
                @foreach($types as $t)
                <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="prm-filter">
            <select name="placement" onchange="this.form.submit()">
                <option value="">All placements</option>
                @foreach($placements as $pl)
                <option value="{{ $pl }}" @selected(request('placement') === $pl)>{{ str_replace('_',' ',ucfirst($pl)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="prm-filter">
            <select name="status" onchange="this.form.submit()">
                <option value="">All status</option>
                <option value="active"   @selected(request('status') === 'active')>Active</option>
                <option value="draft"    @selected(request('status') === 'draft')>Draft</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                <option value="trashed"  @selected(request('status') === 'trashed')>Archived</option>
            </select>
        </div>
        <button type="submit" style="width:auto;margin:0;padding:10px 16px;border-radius:10px;background:#8a1f5e;color:#fff;border:0;cursor:pointer;font:700 13px inherit">Search</button>
        @if(request()->hasAny(['search','type','placement','status']))
        <a href="{{ route('admin.promotions.index') }}" style="padding:10px 14px;border-radius:10px;border:1px solid #e0d0ec;color:#7a3060;font-size:13px;font-weight:600;text-decoration:none;background:#fff">Clear</a>
        @endif
    </form>

    <div class="prm-board">
        @if($promotions->isEmpty())
        <div class="prm-empty">
            <div class="prm-empty-icon">📣</div>
            <h3>No promotions yet</h3>
            <p>Create your first banner or promotion to display in the mobile app.</p>
            @can('promotions.create')
            <a href="{{ route('admin.promotions.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:11px 18px;border-radius:10px;color:#fff;background:#8a1f5e;font-size:13px;font-weight:700;text-decoration:none;margin-top:4px">＋ New Promotion</a>
            @endcan
        </div>
        @else
        <table class="prm-table">
            <thead>
                <tr>
                    <th style="width:70px">Image</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Placement</th>
                    <th>Audience</th>
                    <th>Status</th>
                    <th>Schedule</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($promotions as $p)
            <tr style="{{ $p->trashed() ? 'opacity:.55' : '' }}">
                <td>
                    <div class="prm-thumb">
                        @if($p->image)
                        <img src="{{ Storage::url($p->image) }}" alt="">
                        @else
                        📣
                        @endif
                    </div>
                </td>
                <td>
                    <div class="prm-title">
                        <b>{{ $p->title }}</b>
                        <span>{{ $p->cta_type !== 'none' ? 'CTA: '.ucfirst($p->cta_type) : 'No CTA' }}</span>
                    </div>
                </td>
                <td><span class="prm-chip prm-type-{{ $p->type }}">{{ ucfirst($p->type) }}</span></td>
                <td style="font-size:13px">{{ str_replace('_',' ',ucfirst($p->placement)) }}</td>
                <td style="font-size:13px">{{ ucfirst($p->audience) }}</td>
                <td>
                    @if($p->trashed())
                        <span class="prm-status-archived">Archived</span>
                    @else
                        <span class="prm-status-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                    @endif
                </td>
                <td style="font-size:11px;color:#9a7a90;white-space:nowrap">
                    {{ $p->starts_at?->format('d M') ?? '—' }} → {{ $p->ends_at?->format('d M') ?? '∞' }}
                </td>
                <td>
                    <div class="action-cell">
                        @if($p->trashed())
                            @can('promotions.restore')
                            <form method="POST" action="{{ route('admin.promotions.restore', $p->id) }}" style="display:inline">
                                @csrf <button class="btn-icon success" title="Restore">↩</button>
                            </form>
                            @endcan
                        @else
                            @can('promotions.update')
                            <a href="{{ route('admin.promotions.edit', $p) }}" class="btn-icon" title="Edit">✎</a>
                            @if($p->status === 'active')
                            <form method="POST" action="{{ route('admin.promotions.deactivate', $p) }}" style="display:inline">
                                @csrf <button class="btn-icon warning" title="Deactivate">⏸</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.promotions.activate', $p) }}" style="display:inline">
                                @csrf <button class="btn-icon success" title="Activate">▶</button>
                            </form>
                            @endif
                            @endcan
                            @can('promotions.delete')
                            <form method="POST" action="{{ route('admin.promotions.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Archive this promotion?')">
                                @csrf @method('DELETE')
                                <button class="btn-icon danger" title="Archive">🗑</button>
                            </form>
                            @endcan
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @if($promotions->hasPages())
        <div class="pagination-wrap">{{ $promotions->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection
