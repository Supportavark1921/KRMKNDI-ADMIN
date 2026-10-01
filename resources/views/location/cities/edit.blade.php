@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.cities.index') }}">← Cities</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:6px">Edit {{ $city->name }}</h1>
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('admin.location.cities.update', $city) }}">
            @csrf @method('PUT')

            <label>State</label>
            <input type="text" value="{{ $city->state->name }}" disabled style="background:#f7f8fc;color:#69758b">

            <label>District *</label>
            <select name="district_id" required>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" @selected(old('district_id',$city->district_id)==$d->id)>{{ $d->name }}</option>
                @endforeach
            </select>

            <label>City Name *</label>
            <input type="text" name="name" value="{{ old('name', $city->name) }}" required maxlength="150">

            <label>Status</label>
            <select name="status">
                <option value="active" @selected(old('status',$city->status)==='active')>Active</option>
                <option value="inactive" @selected(old('status',$city->status)==='inactive')>Inactive</option>
            </select>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
@endsection
