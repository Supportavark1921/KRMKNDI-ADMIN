@extends('layouts.app')
@section('title', 'New Role')
@section('content')
<style>
.rol-edit-page{background:radial-gradient(circle at 92% 4%,#d3f9d8 0,transparent 20%),#f5fbf6!important}
.rol-edit-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1020px;margin:35px auto 28px;padding:32px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#63e6be 0,transparent 26%),linear-gradient(118deg,#0a3d28,#1b7a4e)}
.rol-edit-hero h1{margin:6px 0 0;font-size:28px;color:#fff}
.rol-edit-hero p{margin:5px 0 0;color:#b2f2d6;font-size:14px}
.rol-overline{color:#ffd787;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.rol-edit-card{max-width:1020px;margin:0 auto;border:1px solid #d3edd9;border-radius:20px;background:#fff;box-shadow:0 18px 45px #0a3d2809;overflow:hidden}
.rol-name-section{padding:26px 28px;border-bottom:1px solid #e8f5eb}
.rol-section-title{font-size:12px;font-weight:800;color:#1b5e35;letter-spacing:.07em;text-transform:uppercase;margin:0 0 14px}
.rol-input{padding:11px 14px;border:1px solid #c8e6ce;border-radius:10px;font:14px/1.4 inherit;color:#1b2240;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;width:100%;max-width:360px}
.rol-input:focus{border-color:#1b7a4e;box-shadow:0 0 0 3px #d3f9d8}
.rol-input.is-invalid{border-color:#e04a4a}
.rol-label{font-size:13px;font-weight:700;color:#1b2240;display:block;margin-bottom:6px}
.rol-hint{font-size:12px;color:#6b8c76;margin-top:4px}
.rol-error{font-size:12px;color:#e04a4a;margin-top:4px}
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
.flash-error{display:flex;align-items:center;gap:10px;max-width:1020px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fce8e8;color:#962a2a;font-size:14px;font-weight:600}
</style>

<div class="dashboard rol-edit-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small><a href="{{ route('admin.roles.index') }}" style="color:inherit">Roles</a> / New Role</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if($errors->any())
    <div class="flash-error">✗ Please fix the errors below before saving.</div>
    @endif

    <div class="rol-edit-hero">
        <div>
            <span class="rol-overline">Roles &amp; Permissions · Create</span>
            <h1>New Role</h1>
            <p>Name the role and select which permissions it should have from the start.</p>
        </div>
        <div style="font-size:48px">🔐</div>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        <div class="rol-edit-card">

            <div class="rol-name-section">
                <p class="rol-section-title">Role Details</p>
                <label class="rol-label">Role name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="rol-input @error('name') is-invalid @enderror"
                    placeholder="e.g. accountant" required pattern="[a-z0-9\-_]+">
                <p class="rol-hint">Lowercase letters, numbers, hyphens and underscores only.</p>
                @error('name')<p class="rol-error">{{ $message }}</p>@enderror
            </div>

            <div class="rol-matrix-head">
                <h2>Permission Matrix</h2>
                <small>{{ count($allPerms) }} permissions across {{ count($permissionMap) }} menus</small>
            </div>
            <div class="rol-matrix-wrap">
                @php $rolePerms = []; @endphp
                @include('admin.roles._matrix')
            </div>

            <div class="rol-form-footer">
                <button type="submit" class="rol-submit">Create Role</button>
                <a href="{{ route('admin.roles.index') }}" class="rol-cancel">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
