@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('content')
<style>
.rol-page{background:radial-gradient(circle at 92% 4%,#d9f5ec 0,transparent 22%),#f5faf8!important}
.rol-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1200px;margin:35px auto 22px;padding:38px 44px;border-radius:22px;color:#fff;background:radial-gradient(circle at 86% 10%,#38d9a9 0,transparent 26%),linear-gradient(118deg,#0d2e28,#1a5c4c)}
.rol-hero h1{margin:8px 0;font-size:38px;color:#fff}
.rol-hero p{max-width:600px;margin:0;color:#bfece1;line-height:1.6}
.rol-overline{color:#ffd787;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.rol-add-btn{display:flex;align-items:center;gap:8px;flex:0 0 auto;padding:13px 18px;border-radius:12px;color:#1c3d2e;background:linear-gradient(120deg,#ffdd7c,#f0ab4d);font-size:14px;font-weight:800;white-space:nowrap;text-decoration:none;box-shadow:0 10px 22px #08142f26;transition:transform .15s}
.rol-add-btn:hover{transform:translateY(-2px)}
.rol-board{max-width:1200px;margin:0 auto;border:1px solid #d5ede7;border-radius:20px;background:#fff;box-shadow:0 18px 45px #0d2e2809;overflow:hidden}
.rol-table{width:100%;border-collapse:collapse}
.rol-table thead th{padding:13px 16px;border-bottom:1px solid #e4f2ee;background:#f5fbf9;color:#4a7a6e;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;text-align:left}
.rol-table thead th:last-child{text-align:right}
.rol-table tbody tr{border-bottom:1px solid #f0f8f5;transition:background .15s}
.rol-table tbody tr:last-child{border:0}
.rol-table tbody tr:hover{background:#f5fbf9}
.rol-table td{padding:16px;vertical-align:middle}
.rol-name{display:flex;align-items:center;gap:12px}
.rol-icon{display:grid;width:38px;height:38px;place-items:center;border-radius:11px;background:#e0f5ef;color:#1a7a60;font-size:18px;flex-shrink:0}
.rol-name b{display:block;font-size:15px;color:#0d2e28}
.rol-name small{font-size:12px;color:#7a9e97}
.perm-count{display:inline-block;padding:5px 12px;border-radius:20px;color:#1a6b47;background:#e2f7ed;font-size:13px;font-weight:750}
.user-count{display:inline-block;padding:5px 12px;border-radius:20px;color:#2c5f9e;background:#dbeafe;font-size:13px;font-weight:750}
.action-cell{display:flex;align-items:center;justify-content:flex-end;gap:6px}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font-size:14px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#1a7a60;color:#1a7a60;background:#e0f5ef}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.flash-error{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fce8e8;color:#962a2a;font-size:14px;font-weight:600}
</style>

<div class="dashboard rol-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small>Roles &amp; Permissions</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error">✗ {{ session('error') }}</div>@endif

    <div class="rol-hero">
        <div>
            <span class="rol-overline">Admin · Access Control</span>
            <h1>Roles &amp; Permissions</h1>
            <p>Define what each role can see and do. Changes take effect immediately across the platform.</p>
        </div>
        @can('roles.create')
        <a href="{{ route('admin.roles.create') }}" class="rol-add-btn">＋ New Role</a>
        @endcan
    </div>

    <div class="rol-board">
        <table class="rol-table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
            <tr>
                <td>
                    <div class="rol-name">
                        <div class="rol-icon">🔑</div>
                        <div>
                            <b>{{ ucfirst($role->name) }}</b>
                            <small>{{ $role->name === 'admin' ? 'Full system access' : ($role->name === 'manager' ? 'Broad operational access' : ($role->name === 'support' ? 'View & assist clients' : ($role->name === 'guruji' ? 'Pooja & orders' : ($role->name === 'vendor' ? 'Own store products' : 'Mobile app user')))) }}</small>
                        </div>
                    </div>
                </td>
                <td><span class="perm-count">{{ $role->permissions_count }} permissions</span></td>
                <td><span class="user-count">{{ $role->users_count }} users</span></td>
                <td>
                    <div class="action-cell">
                        @can('roles.update')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn-icon" title="Edit permissions">✎</a>
                        @endcan
                        @if(!in_array($role->name, ['admin','user']))
                        @can('roles.delete')
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="display:inline" onsubmit="return confirm('Delete role {{ $role->name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn-icon danger" title="Delete">🗑</button>
                        </form>
                        @endcan
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
