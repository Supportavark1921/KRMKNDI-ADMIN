@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.districts.index') }}">← Districts</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:6px">Add District</h1>
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('admin.location.districts.store') }}">
            @csrf
            <label>State / UT *</label>
            <select name="state_id" required>
                <option value="">Select state</option>
                @foreach($states as $s)
                    <option value="{{ $s->id }}" @selected(old('state_id')==$s->id)>{{ $s->name }} ({{ $s->code }})</option>
                @endforeach
            </select>

            <label>District Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="150">

            <label>Code <em style="font-weight:400">(optional)</em></label>
            <input type="text" name="code" value="{{ old('code') }}" maxlength="20">

            <label>Status</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit">Add District</button>
        </form>
    </div>
</div>
@endsection
