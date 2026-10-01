@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.districts.index') }}">← Districts</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:6px">Edit {{ $district->name }}</h1>
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('admin.location.districts.update', $district) }}">
            @csrf @method('PUT')

            <label>State</label>
            <input type="text" value="{{ $district->state->name }}" disabled style="background:#f7f8fc;color:#69758b">

            <label>District Name *</label>
            <input type="text" name="name" value="{{ old('name', $district->name) }}" required maxlength="150">

            <label>Code</label>
            <input type="text" name="code" value="{{ old('code', $district->code) }}" maxlength="20">

            <label>Status</label>
            <select name="status">
                <option value="active" @selected(old('status',$district->status)==='active')>Active</option>
                <option value="inactive" @selected(old('status',$district->status)==='inactive')>Inactive</option>
            </select>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
@endsection
