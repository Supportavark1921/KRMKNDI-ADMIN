@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">Roles &amp; Permissions</h1>
        <p class="store-sub">Manage who can do what across the system.</p>
    </div>
    @can('roles.create')
    <a href="{{ route('admin.roles.create') }}" class="btn-primary">+ New Role</a>
    @endcan
</div>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

<div class="store-card">
    <table class="store-table">
        <thead><tr><th>Role</th><th>Permissions</th><th>Users</th><th></th></tr></thead>
        <tbody>
        @foreach($roles as $role)
        <tr>
            <td><strong>{{ $role->name }}</strong></td>
            <td>{{ $role->permissions_count }}</td>
            <td>{{ $role->users_count }}</td>
            <td style="text-align:right;white-space:nowrap">
                @can('roles.update')
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn-sm">Edit permissions</a>
                @endcan
                @if(!in_array($role->name, ['admin','user']))
                @can('roles.delete')
                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="display:inline">
                    @csrf @method('DELETE')
                    <button class="btn-sm btn-danger" onclick="return confirm('Delete role {{ $role->name }}?')">Delete</button>
                </form>
                @endcan
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
