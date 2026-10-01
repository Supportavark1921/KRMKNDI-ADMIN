@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.states.index') }}">← States</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:6px">Edit {{ $state->name }}</h1>
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('admin.location.states.update', $state) }}">
            @csrf @method('PUT')

            <label>Country</label>
            <input type="text" value="{{ $state->country->name }}" disabled style="background:#f7f8fc;color:#69758b">

            <label>Name *</label>
            <input type="text" name="name" value="{{ old('name', $state->name) }}" required maxlength="150">

            <div class="form-grid" style="margin-top:0">
                <div>
                    <label>Code</label>
                    <input type="text" name="code" value="{{ old('code', $state->code) }}" maxlength="10">
                </div>
                <div>
                    <label>Type *</label>
                    <select name="type">
                        <option value="STATE" @selected(old('type',$state->type)==='STATE')>State</option>
                        <option value="UNION_TERRITORY" @selected(old('type',$state->type)==='UNION_TERRITORY')>Union Territory</option>
                    </select>
                </div>
            </div>

            <label>Status</label>
            <select name="status">
                <option value="active" @selected(old('status',$state->status)==='active')>Active</option>
                <option value="inactive" @selected(old('status',$state->status)==='inactive')>Inactive</option>
            </select>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
@endsection
