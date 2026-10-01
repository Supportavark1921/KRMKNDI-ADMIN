@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.states.index') }}">← States</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:6px">Add State / UT</h1>
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('admin.location.states.store') }}">
            @csrf
            <label>Country *</label>
            <select name="country_id" required>
                <option value="">Select country</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" @selected(old('country_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>

            <label>Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="150">

            <div class="form-grid" style="margin-top:0">
                <div>
                    <label>Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" maxlength="10" placeholder="MP">
                </div>
                <div>
                    <label>Type *</label>
                    <select name="type">
                        <option value="STATE" @selected(old('type','STATE')==='STATE')>State</option>
                        <option value="UNION_TERRITORY" @selected(old('type')==='UNION_TERRITORY')>Union Territory</option>
                    </select>
                </div>
            </div>

            <label>Status</label>
            <select name="status">
                <option value="active" @selected(old('status','active')==='active')>Active</option>
                <option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
            </select>

            <button type="submit">Add State/UT</button>
        </form>
    </div>
</div>
@endsection
