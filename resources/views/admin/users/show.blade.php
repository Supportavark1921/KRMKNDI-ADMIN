@extends('layouts.app')
@section('title', $user->name)
@section('content')
<style>
.ux-show-page{background:radial-gradient(circle at 92% 4%,#dde4ff 0,transparent 22%),#f5f6fc!important}
.ux-show-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:900px;margin:35px auto 28px;padding:30px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#748ffc 0,transparent 26%),linear-gradient(118deg,#1c2a5e,#364bbf)}
.ux-show-hero h1{margin:6px 0 0;font-size:26px;color:#fff}
.ux-show-hero p{margin:5px 0 0;color:#ccd4f8;font-size:13px}
.ux-overline{color:#ffd787;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.ux-avatar-xl{display:grid;width:64px;height:64px;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(135deg,#748ffc,#4c6ef5);font-size:26px;font-weight:800;flex-shrink:0}
.ux-show-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:900px;margin:0 auto}
.ux-show-card{background:#fff;border:1px solid #e4e7f2;border-radius:16px;padding:22px 24px}
.ux-show-card h3{margin:0 0 16px;font-size:13px;font-weight:800;color:#6b748c;letter-spacing:.06em;text-transform:uppercase}
.ux-actions{display:flex;gap:8px;flex-wrap:wrap}
.ux-btn{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border:0;border-radius:9px;cursor:pointer;font:700 13px inherit;text-decoration:none;transition:opacity .15s}
.ux-btn:hover{opacity:.85}
.ux-btn-edit{color:#fff;background:linear-gradient(100deg,#3b5bdb,#5578f0)}
.ux-btn-suspend{color:#2d1f00;background:#ffc107}
.ux-btn-activate{color:#fff;background:#28a745}
.ux-btn-back{color:#555e7a;background:#eef0f8}
.flash-success{max-width:900px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#d3f9d8;color:#1a4d2a;font-size:14px;font-weight:600}
@media(max-width:700px){.ux-show-grid{grid-template-columns:1fr}}
</style>

<div class="dashboard ux-show-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small><a href="{{ route('admin.users.index') }}" style="color:inherit">Users</a> / {{ $user->name }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

    <div class="ux-show-hero">
        <div style="display:flex;align-items:center;gap:18px">
            <div class="ux-avatar-xl">{{ strtoupper(substr($user->name,0,1)) }}</div>
            <div>
                <span class="ux-overline">Users · Profile</span>
                <h1>{{ $user->name }}</h1>
                <p>{{ $user->email }} · Joined {{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>
        <div class="ux-actions">
            @can('users.update')
            <a href="{{ route('admin.users.edit', $user) }}" class="ux-btn ux-btn-edit">✎ Edit</a>
            @endcan
            @if($user->status === 'active' && $user->id !== auth()->id())
                @can('users.update')
                <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                    @csrf<button class="ux-btn ux-btn-suspend" onclick="return confirm('Suspend this user?')">⏸ Suspend</button>
                </form>
                @endcan
            @elseif($user->status === 'suspended')
                @can('users.update')
                <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                    @csrf<button class="ux-btn ux-btn-activate">▶ Activate</button>
                </form>
                @endcan
            @endif
        </div>
    </div>

    <div class="ux-show-grid">
        <div class="ux-show-card">
            <h3>Account</h3>
            <dl class="detail-list">
                <dt>Email</dt><dd>{{ $user->email }}</dd>
                <dt>Role</dt><dd><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></dd>
                <dt>Status</dt><dd>
                    @if($user->trashed()) <span class="badge badge-inactive">Archived</span>
                    @elseif($user->status === 'suspended') <span class="badge badge-inactive">Suspended</span>
                    @else <span class="badge badge-active">Active</span>
                    @endif
                </dd>
                <dt>Created</dt><dd>{{ $user->created_at->format('d M Y H:i') }}</dd>
            </dl>
        </div>

        <div class="ux-show-card">
            <h3>Roles &amp; Permissions</h3>
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
                @forelse($user->roles as $r)
                    <span class="badge badge-active">{{ $r->name }}</span>
                @empty
                    <p style="color:#888;margin:0;font-size:13px">No roles assigned.</p>
                @endforelse
            </div>
            @if($user->permissions->count())
            <p style="font-size:11px;font-weight:800;color:#6b748c;letter-spacing:.06em;text-transform:uppercase;margin:14px 0 8px">Direct Permissions</p>
            <div style="display:flex;flex-wrap:wrap;gap:4px">
                @foreach($user->permissions as $p)
                <span class="badge" style="background:#f0f4ff;color:#3730a3;font-size:11px">{{ $p->name }}</span>
                @endforeach
            </div>
            @endif
        </div>

        @if($user->vendor)
        <div class="ux-show-card">
            <h3>Vendor Profile</h3>
            <dl class="detail-list">
                <dt>Business</dt><dd>{{ $user->vendor->business_name }}</dd>
                <dt>Status</dt><dd>{{ ucfirst($user->vendor->status) }}</dd>
            </dl>
            <a href="{{ route('admin.store.vendors.show', $user->vendor) }}" class="ux-btn ux-btn-back" style="margin-top:14px;display:inline-flex">View vendor →</a>
        </div>
        @endif
    </div>

    <div style="max-width:900px;margin:20px auto 0">
        <a href="{{ route('admin.users.index') }}" class="ux-btn ux-btn-back">← Back to users</a>
    </div>
</div>
@endsection
