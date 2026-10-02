@extends('layouts.app')
@section('title', 'Edit Role: ' . $role->name)
@section('content')
<style>
.rol-edit-page{background:radial-gradient(circle at 92% 4%,#d3f9d8 0,transparent 20%),#f5fbf6!important}
.rol-edit-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1020px;margin:35px auto 28px;padding:32px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#63e6be 0,transparent 26%),linear-gradient(118deg,#0a3d28,#1b7a4e)}
.rol-edit-hero h1{margin:6px 0 0;font-size:28px;color:#fff}
.rol-edit-hero p{margin:5px 0 0;color:#b2f2d6;font-size:14px}
.rol-overline{color:#ffd787;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.rol-edit-card{max-width:1020px;margin:0 auto;border:1px solid #d3edd9;border-radius:20px;background:#fff;box-shadow:0 18px 45px #0a3d2809;overflow:hidden}
.rol-matrix-head{display:flex;align-items:center;justify-content:space-between;padding:22px 28px;border-bottom:1px solid #e8f5eb}
.rol-matrix-head h2{font-size:14px;font-weight:800;color:#1b5e35;margin:0}
.rol-matrix-head small{font-size:12px;color:#6b8c76}
.rol-matrix-wrap{overflow-x:auto;padding:0 8px 8px}
.rol-matrix-table{width:100%;border-collapse:collapse;min-width:560px}
.rol-matrix-table th{padding:10px 14px;font-size:11px;font-weight:800;color:#6b8c76;letter-spacing:.06em;text-transform:uppercase;border-bottom:2px solid #e8f5eb;text-align:center}
.rol-matrix-table th:first-child{text-align:left}
.rol-matrix-table td{padding:12px 14px;border-bottom:1px solid #f0f8f2;vertical-align:middle}
.rol-matrix-table tr:last-child td{border-bottom:0}
.rol-matrix-table tr:hover td{background:#f7fdf9}
.rol-menu-name{font-size:13px;font-weight:700;color:#1b2240}
.rol-cb-wrap{display:flex;justify-content:center}
.rol-cb{width:17px;height:17px;accent-color:#1b7a4e;cursor:pointer}
.rol-dash{font-size:15px;color:#d0e8d6;display:block;text-align:center}
.rol-form-footer{display:flex;align-items:center;gap:12px;padding:22px 28px;background:#f5fbf7;border-top:1px solid #e8f5eb}
.rol-submit{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border:0;border-radius:10px;cursor:pointer;color:#fff;background:linear-gradient(100deg,#1b7a4e,#28a865);box-shadow:0 6px 16px #1b7a4e2a;font:700 14px inherit;transition:opacity .15s,transform .1s}
.rol-submit:hover{opacity:.9;transform:translateY(-1px)}
.rol-cancel{display:inline-flex;align-items:center;padding:12px 18px;border-radius:10px;border:1px solid #d3edd9;color:#1b5e35;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.rol-cancel:hover{background:#f5fbf7}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1020px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#d3f9d8;color:#1a4d2a;font-size:14px;font-weight:600}
.rol-check-all{font-size:11px;color:#1b7a4e;cursor:pointer;text-decoration:underline;background:none;border:0;padding:0;margin-left:6px}
</style>

<div class="dashboard rol-edit-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small><a href="{{ route('admin.roles.index') }}" style="color:inherit">Roles</a> / Edit</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))
    <div class="flash-success">✓ {{ session('success') }}</div>
    @endif

    <div class="rol-edit-hero">
        <div>
            <span class="rol-overline">Roles &amp; Permissions · Edit</span>
            <h1>{{ ucfirst($role->name) }}</h1>
            <p>Toggle the permissions this role has. Changes apply to all users in this role.</p>
        </div>
        <div style="font-size:48px">🔐</div>
    </div>

    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf @method('PUT')

        <div class="rol-edit-card">
            <div class="rol-matrix-head">
                <h2>Permission Matrix</h2>
                <small>{{ count($allPerms) }} permissions across {{ count($permissionMap) }} menus</small>
            </div>
            <div class="rol-matrix-wrap">
                @include('admin.roles._matrix')
            </div>
            <div class="rol-form-footer">
                <button type="submit" class="rol-submit">Save Permissions</button>
                <a href="{{ route('admin.roles.index') }}" class="rol-cancel">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
