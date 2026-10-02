@extends('layouts.app')
@section('title', 'Edit Role: ' . $role->name)
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">Role: {{ $role->name }}</h1>
        <p class="store-sub"><a href="{{ route('admin.roles.index') }}">Roles</a> / Edit permissions</p>
    </div>
</div>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

<form method="POST" action="{{ route('admin.roles.update', $role) }}">
    @csrf @method('PUT')

    @include('admin.roles._matrix')

    <div style="margin-top:16px;display:flex;gap:10px">
        <button type="submit" class="btn-primary">Save Permissions</button>
        <a href="{{ route('admin.roles.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>
@endsection
