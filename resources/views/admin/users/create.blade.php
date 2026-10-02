@extends('layouts.app')
@section('title', 'New User')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">New User</h1>
        <p class="store-sub"><a href="{{ route('admin.users.index') }}">Users</a> / Create</p>
    </div>
</div>
<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    @include('admin.users._form')
</form>
@endsection
