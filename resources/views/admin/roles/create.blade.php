@extends('layouts.app')
@section('title', 'New Role')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">New Role</h1>
        <p class="store-sub"><a href="{{ route('admin.roles.index') }}">Roles</a> / Create</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf
    <div class="store-card" style="max-width:400px;margin-bottom:16px">
        <div class="form-group">
            <label class="form-label">Role name * <small style="color:#888">(lowercase, letters/numbers/hyphens)</small></label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-input @error('name') is-invalid @enderror"
                placeholder="e.g. accountant" required pattern="[a-z0-9\-_]+">
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>
    </div>

    @php $rolePerms = []; @endphp
    @include('admin.roles._matrix')

    <div style="margin-top:16px;display:flex;gap:10px">
        <button type="submit" class="btn-primary">Create Role</button>
        <a href="{{ route('admin.roles.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>
@endsection
