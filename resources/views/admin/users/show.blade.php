@extends('layouts.app')
@section('title', $user->name)
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">{{ $user->name }}</h1>
        <p class="store-sub"><a href="{{ route('admin.users.index') }}">Users</a> / {{ $user->name }}</p>
    </div>
    <div style="display:flex;gap:8px">
        @can('users.update')
        <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary">Edit</a>
        @endcan
        @if($user->status === 'active' && $user->id !== auth()->id())
        @can('users.update')
        <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
            @csrf <button class="btn-secondary btn-warning" onclick="return confirm('Suspend this user?')">Suspend</button>
        </form>
        @endcan
        @elseif($user->status === 'suspended')
        @can('users.update')
        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
            @csrf <button class="btn-secondary btn-success">Activate</button>
        </form>
        @endcan
        @endif
    </div>
</div>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div class="store-card">
        <h3 style="margin:0 0 12px">Account</h3>
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

    <div class="store-card">
        <h3 style="margin:0 0 12px">Spatie Roles</h3>
        @forelse($user->roles as $r)
            <span class="badge badge-active">{{ $r->name }}</span>
        @empty
            <p style="color:#888;margin:0">No Spatie roles assigned.</p>
        @endforelse

        @if($user->permissions->count())
        <h3 style="margin:16px 0 8px">Direct Permissions</h3>
        <div style="display:flex;flex-wrap:wrap;gap:4px">
            @foreach($user->permissions as $p)
            <span class="badge" style="background:#f0f4ff;color:#3730a3;font-size:11px">{{ $p->name }}</span>
            @endforeach
        </div>
        @endif
    </div>

    @if($user->vendor)
    <div class="store-card">
        <h3 style="margin:0 0 12px">Vendor Profile</h3>
        <dl class="detail-list">
            <dt>Business</dt><dd>{{ $user->vendor->business_name }}</dd>
            <dt>Status</dt><dd>{{ ucfirst($user->vendor->status) }}</dd>
        </dl>
        <a href="{{ route('admin.store.vendors.show', $user->vendor) }}" class="btn-sm" style="margin-top:8px">View vendor profile →</a>
    </div>
    @endif
</div>
@endsection
