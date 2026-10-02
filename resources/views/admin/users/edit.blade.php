@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">Edit: {{ $user->name }}</h1>
        <p class="store-sub"><a href="{{ route('admin.users.index') }}">Users</a> / <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a> / Edit</p>
    </div>
</div>
<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf @method('PUT')
    @include('admin.users._form')
</form>
@endsection
