@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.countries.index') }}">← Countries</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:6px">Add Country</h1>
        <p style="color:#69758b;margin-bottom:28px">Add a new country to the location master.</p>

        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif

        <form method="post" action="{{ route('admin.location.countries.store') }}">
            @csrf
            <label>Country Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="100">

            <div class="form-grid" style="margin-top:0">
                <div>
                    <label>ISO Code (2 letters) *</label>
                    <input type="text" name="iso_code" value="{{ old('iso_code') }}" required maxlength="2" placeholder="IN" style="text-transform:uppercase">
                </div>
                <div>
                    <label>Currency Code</label>
                    <input type="text" name="currency_code" value="{{ old('currency_code') }}" maxlength="5" placeholder="INR">
                </div>
            </div>

            <label>Phone Code</label>
            <input type="text" name="phone_code" value="{{ old('phone_code', '+') }}" maxlength="10" placeholder="+91">

            <label>Status</label>
            <select name="status">
                <option value="active" @selected(old('status','active')==='active')>Active</option>
                <option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
            </select>

            <button type="submit">Add Country</button>
        </form>
    </div>
</div>
@endsection
