@extends('layouts.app')
@section('title', 'Users')
@section('content')
<style>
:root{--ux:#3b5bdb;--ux-lt:#e8edff;--ux-dk:#2f4ac0;}
.ux-page{background:radial-gradient(circle at 92% 4%,#dde4ff 0,transparent 22%),#f5f6fc!important}
.ux-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1200px;margin:35px auto 22px;padding:38px 44px;border-radius:22px;color:#fff;background:radial-gradient(circle at 86% 10%,#748ffc 0,transparent 26%),linear-gradient(118deg,#1c2a5e,#364bbf)}
.ux-hero h1{margin:8px 0;font-size:38px;color:#fff}
.ux-hero p{max-width:600px;margin:0;color:#ccd4f8;line-height:1.6}
.ux-overline{color:#ffd787;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.ux-add-btn{display:flex;align-items:center;gap:8px;flex:0 0 auto;padding:13px 18px;border-radius:12px;color:#1c2d6b;background:linear-gradient(120deg,#ffdd7c,#f0ab4d);font-size:14px;font-weight:800;white-space:nowrap;text-decoration:none;box-shadow:0 10px 22px #08142f26;transition:transform .15s}
.ux-add-btn:hover{transform:translateY(-2px)}
.ux-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;max-width:1200px;margin:0 auto 18px}
.ux-stat{display:flex;align-items:center;gap:12px;padding:17px 20px;border:1px solid #e6e8f0;border-radius:15px;background:#fff}
.ux-stat-icon{display:grid;width:38px;height:38px;place-items:center;border-radius:11px;font-size:18px}
.ux-stat-icon.all{color:#3b5bdb;background:#e8edff}
.ux-stat-icon.active{color:#1e6b47;background:#e2f7ed}
.ux-stat-icon.suspended{color:#a06020;background:#fff3d2}
.ux-stat-icon.archived{color:#666;background:#eee}
.ux-stat strong,.ux-stat small{display:block}
.ux-stat strong{color:#1b2240;font-size:20px}
.ux-stat small{color:#7882a0;font-size:12px}
.ux-toolbar{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 18px;flex-wrap:wrap}
.ux-search{flex:1;min-width:200px;position:relative}
.ux-search input{padding:11px 14px 11px 40px;border:1px solid #e0e3ef;border-radius:11px;font:inherit;color:#1e2640;background:#fff;width:100%;outline:none;transition:.2s}
.ux-search input:focus{border-color:#3b5bdb;box-shadow:0 0 0 3px #e8edff}
.ux-search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9aa3bc;font-size:15px}
.ux-filter select{padding:10px 12px;border:1px solid #e0e3ef;border-radius:11px;font:inherit;color:#3a4060;background:#fff;outline:none;cursor:pointer}
.ux-filter select:focus{border-color:#3b5bdb}
.ux-board{max-width:1200px;margin:0 auto;border:1px solid #e4e7f2;border-radius:20px;background:#fff;box-shadow:0 18px 45px #1e2a5a09;overflow:hidden}
.ux-table{width:100%;border-collapse:collapse}
.ux-table thead th{padding:13px 16px;border-bottom:1px solid #eef0f7;background:#fafbff;color:#6b748c;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;text-align:left}
.ux-table thead th:last-child{text-align:right}
.ux-table tbody tr{border-bottom:1px solid #f0f2f8;transition:background .15s}
.ux-table tbody tr:last-child{border:0}
.ux-table tbody tr:hover{background:#fafbff}
.ux-table td{padding:14px 16px;vertical-align:middle}
.ux-avatar{display:grid;width:38px;height:38px;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(135deg,#748ffc,#4c6ef5);font-size:14px;font-weight:800;flex-shrink:0}
.ux-name-cell{display:flex;align-items:center;gap:12px}
.ux-name b{display:block;font-size:14px;color:#1b2240;margin-bottom:2px}
.ux-name span{font-size:12px;color:#7882a0}
.ux-role-chip{display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:750}
.ux-role-admin{color:#fff;background:#3b5bdb}
.ux-role-manager{color:#1e4d8c;background:#dbeafe}
.ux-role-support{color:#5a3a8e;background:#ede9ff}
.ux-role-guruji{color:#7a4205;background:#fff0de}
.ux-role-vendor{color:#1f6b5a;background:#d7f5ec}
.ux-role-user{color:#444f6a;background:#eef0f8}
.ux-status-active{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.ux-status-suspended{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#a06020;background:#fff3d2;font-size:11px;font-weight:750}
.ux-status-archived{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#666;background:#eee;font-size:11px;font-weight:750}
.ux-status-active::before,.ux-status-suspended::before,.ux-status-archived::before{content:"";width:6px;height:6px;border-radius:50%}
.ux-status-active::before{background:#28a76a}
.ux-status-suspended::before{background:#f0a42e}
.ux-status-archived::before{background:#aaa}
.action-cell{display:flex;align-items:center;justify-content:flex-end;gap:6px}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font-size:14px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#3b5bdb;color:#3b5bdb;background:#e8edff}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.btn-icon.success:hover{border-color:#28a76a;color:#28a76a;background:#e2f7ed}
.btn-icon.warning:hover{border-color:#f0a42e;color:#a06020;background:#fff3d2}
.ux-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:72px 24px;text-align:center}
.ux-empty-icon{display:grid;width:60px;height:60px;place-items:center;border-radius:18px;background:#e8edff;color:#3b5bdb;font-size:30px;margin:0 auto 16px}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.flash-error{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fce8e8;color:#962a2a;font-size:14px;font-weight:600}
.pagination-wrap{display:flex;justify-content:center;padding:20px;border-top:1px solid #f0f2f8}
</style>

<div class="dashboard ux-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small>User Management</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error">✗ {{ session('error') }}</div>@endif

    {{-- Hero --}}
    <div class="ux-hero">
        <div>
            <span class="ux-overline">Admin · Users</span>
            <h1>User Management</h1>
            <p>Create and manage user accounts, assign roles and control access across the platform.</p>
        </div>
        @can('users.create')
        <a href="{{ route('admin.users.create') }}" class="ux-add-btn">＋ New User</a>
        @endcan
    </div>

    {{-- Stats --}}
    @php
        $totalUsers     = \App\Models\User::count();
        $activeUsers    = \App\Models\User::where('status','active')->count();
        $suspendedUsers = \App\Models\User::where('status','suspended')->count();
        $archivedUsers  = \App\Models\User::onlyTrashed()->count();
    @endphp
    <div class="ux-stats">
        <div class="ux-stat"><div class="ux-stat-icon all">👥</div><div><strong>{{ $totalUsers }}</strong><small>Total Users</small></div></div>
        <div class="ux-stat"><div class="ux-stat-icon active">●</div><div><strong>{{ $activeUsers }}</strong><small>Active</small></div></div>
        <div class="ux-stat"><div class="ux-stat-icon suspended">●</div><div><strong>{{ $suspendedUsers }}</strong><small>Suspended</small></div></div>
        <div class="ux-stat"><div class="ux-stat-icon archived">●</div><div><strong>{{ $archivedUsers }}</strong><small>Archived</small></div></div>
    </div>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="ux-toolbar">
        <div class="ux-search">
            <span class="ux-search-icon">⌕</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Name or email…" autocomplete="off">
        </div>
        <div class="ux-filter">
            <select name="role" onchange="this.form.submit()">
                <option value="">All Roles</option>
                @foreach($roles as $r)
                <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>
        <div class="ux-filter">
            <select name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active"    @selected(request('status') === 'active')>Active</option>
                <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                <option value="trashed"   @selected(request('status') === 'trashed')>Archived</option>
            </select>
        </div>
        <button type="submit" style="width:auto;margin:0;padding:10px 16px;border-radius:10px;background:var(--violet);box-shadow:none;font-size:13px;color:#fff;border:0;cursor:pointer;font-weight:700">Search</button>
        @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users.index') }}" style="padding:10px 14px;border-radius:10px;border:1px solid #e0e3ef;color:#555e7a;font-size:13px;font-weight:600;text-decoration:none;background:#fff">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="ux-board">
        @if($users->isEmpty())
        <div class="ux-empty">
            <div class="ux-empty-icon">👤</div>
            <h3>No users found</h3>
            <p>Try adjusting your filters or create a new user.</p>
        </div>
        @else
        <table class="ux-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $u)
            <tr style="{{ $u->trashed() ? 'opacity:.55' : '' }}">
                <td>
                    <div class="ux-name-cell">
                        <div class="ux-avatar">{{ strtoupper(substr($u->name,0,1)) }}</div>
                        <div class="ux-name">
                            <b>{{ $u->name }}</b>
                            <span>{{ $u->email }}</span>
                        </div>
                    </div>
                </td>
                <td><span class="ux-role-chip ux-role-{{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                <td>
                    @if($u->trashed())
                        <span class="ux-status-archived">Archived</span>
                    @elseif($u->status === 'suspended')
                        <span class="ux-status-suspended">Suspended</span>
                    @else
                        <span class="ux-status-active">Active</span>
                    @endif
                </td>
                <td style="color:#9aa3bc;font-size:12px">{{ $u->created_at->format('d M Y') }}</td>
                <td>
                    <div class="action-cell">
                        @can('users.view')
                        <a href="{{ route('admin.users.show', $u) }}" class="btn-icon" title="View">👁</a>
                        @endcan
                        @if(!$u->trashed())
                            @can('users.update')
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn-icon" title="Edit">✎</a>
                            @endcan
                            @if($u->status === 'active' && $u->id !== auth()->id())
                            @can('users.update')
                            <form method="POST" action="{{ route('admin.users.suspend', $u) }}" style="display:inline">
                                @csrf <button class="btn-icon warning" title="Suspend">⏸</button>
                            </form>
                            @endcan
                            @elseif($u->status === 'suspended')
                            @can('users.update')
                            <form method="POST" action="{{ route('admin.users.activate', $u) }}" style="display:inline">
                                @csrf <button class="btn-icon success" title="Activate">▶</button>
                            </form>
                            @endcan
                            @endif
                            @if($u->id !== auth()->id())
                            @can('users.delete')
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" style="display:inline" onsubmit="return confirm('Archive {{ addslashes($u->name) }}?')">
                                @csrf @method('DELETE')
                                <button class="btn-icon danger" title="Archive">🗑</button>
                            </form>
                            @endcan
                            @endif
                        @else
                            @can('users.restore')
                            <form method="POST" action="{{ route('admin.users.restore', $u->id) }}" style="display:inline">
                                @csrf <button class="btn-icon success" title="Restore">↩</button>
                            </form>
                            @endcan
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="pagination-wrap">{{ $users->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection
