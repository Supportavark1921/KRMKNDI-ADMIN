@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.countries.index') }}">← Countries</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:6px">Edit Country</h1>
        <p style="color:#69758b;margin-bottom:28px">ISO code cannot be changed after creation.</p>

        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif

        <form method="post" action="{{ route('admin.location.countries.update', $country) }}">
            @csrf @method('PUT')

            <label>Country Name *</label>
            <input type="text" name="name" value="{{ old('name', $country->name) }}" required maxlength="100">

            <label>ISO Code</label>
            <input type="text" value="{{ $country->iso_code }}" disabled style="background:#f7f8fc;color:#69758b">

            <div class="form-grid" style="margin-top:0">
                <div>
                    <label>Currency Code</label>
                    <input type="text" name="currency_code" value="{{ old('currency_code', $country->currency_code) }}" maxlength="5">
                </div>
                <div>
                    <label>Phone Code</label>
                    <input type="text" name="phone_code" value="{{ old('phone_code', $country->phone_code) }}" maxlength="10">
                </div>
            </div>

            <label>Status</label>
            <select name="status">
                <option value="active" @selected(old('status',$country->status)==='active')>Active</option>
                <option value="inactive" @selected(old('status',$country->status)==='inactive')>Inactive</option>
            </select>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
@endsection
