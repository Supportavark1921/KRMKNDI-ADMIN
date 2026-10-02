@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">Users</h1>
        <p class="store-sub">Manage accounts, roles and access.</p>
    </div>
    @can('users.create')
    <a href="{{ route('admin.users.create') }}" class="btn-primary">+ New User</a>
    @endcan
</div>

{{-- Filters --}}
<form method="GET" class="store-filters" style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email…" class="form-input" style="width:200px">
    <select name="role" class="form-input">
        <option value="">All roles</option>
        @foreach($roles as $r)
        <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <select name="status" class="form-input">
        <option value="">All status</option>
        <option value="active"    @selected(request('status') === 'active')>Active</option>
        <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
        <option value="trashed"   @selected(request('status') === 'trashed')>Archived</option>
    </select>
    <button type="submit" class="btn-secondary">Filter</button>
    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Clear</a>
</form>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

<div class="store-card">
    <table class="store-table">
        <thead><tr>
            <th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($users as $u)
        <tr class="{{ $u->trashed() ? 'opacity-50' : '' }}">
            <td><strong>{{ $u->name }}</strong></td>
            <td>{{ $u->email }}</td>
            <td><span class="badge badge-{{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
            <td>
                @if($u->trashed())
                    <span class="badge badge-inactive">Archived</span>
                @elseif($u->status === 'suspended')
                    <span class="badge badge-inactive">Suspended</span>
                @else
                    <span class="badge badge-active">Active</span>
                @endif
            </td>
            <td>{{ $u->created_at->format('d M Y') }}</td>
            <td style="text-align:right;white-space:nowrap">
                @can('users.view')
                <a href="{{ route('admin.users.show', $u) }}" class="btn-sm">View</a>
                @endcan
                @if($u->trashed())
                    @can('users.restore')
                    <form method="POST" action="{{ route('admin.users.restore', $u->id) }}" style="display:inline">
                        @csrf <button class="btn-sm btn-success">Restore</button>
                    </form>
                    @endcan
                @else
                    @can('users.update')
                    <a href="{{ route('admin.users.edit', $u) }}" class="btn-sm">Edit</a>
                    @endcan
                    @if($u->status === 'active' && $u->id !== auth()->id())
                    @can('users.update')
                    <form method="POST" action="{{ route('admin.users.suspend', $u) }}" style="display:inline">
                        @csrf <button class="btn-sm btn-warning" onclick="return confirm('Suspend {{ $u->name }}?')">Suspend</button>
                    </form>
                    @endcan
                    @elseif($u->status === 'suspended')
                    @can('users.update')
                    <form method="POST" action="{{ route('admin.users.activate', $u) }}" style="display:inline">
                        @csrf <button class="btn-sm btn-success">Activate</button>
                    </form>
                    @endcan
                    @endif
                    @if($u->id !== auth()->id())
                    @can('users.delete')
                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn-sm btn-danger" onclick="return confirm('Archive {{ $u->name }}?')">Archive</button>
                    </form>
                    @endcan
                    @endif
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:24px;color:#888">No users found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="padding:12px">{{ $users->links() }}</div>
</div>
@endsection
