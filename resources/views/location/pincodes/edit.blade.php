@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.pincodes.index') }}">← PIN Codes</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:4px">{{ $pincode->pincode }}</h1>
        <p style="color:#69758b;margin-bottom:24px">{{ $pincode->state->name }} → {{ $pincode->district->name }}</p>

        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif

        <form method="post" action="{{ route('admin.location.pincodes.update', $pincode) }}">
            @csrf @method('PUT')

            <label>Post Office Name *</label>
            <input type="text" name="post_office_name" value="{{ old('post_office_name', $pincode->post_office_name) }}" required maxlength="200">

            <div class="form-grid" style="margin-top:0">
                <div>
                    <label>Office Type</label>
                    <select name="office_type">
                        <option value="">—</option>
                        <option value="POST_OFFICE" @selected(old('office_type',$pincode->office_type)==='POST_OFFICE')>Post Office</option>
                        <option value="SUB_POST_OFFICE" @selected(old('office_type',$pincode->office_type)==='SUB_POST_OFFICE')>Sub Post Office</option>
                        <option value="BRANCH_POST_OFFICE" @selected(old('office_type',$pincode->office_type)==='BRANCH_POST_OFFICE')>Branch Post Office</option>
                    </select>
                </div>
                <div>
                    <label>Delivery</label>
                    <select name="delivery_status">
                        <option value="">—</option>
                        <option value="Delivery" @selected(old('delivery_status',$pincode->delivery_status)==='Delivery')>Delivery</option>
                        <option value="Non-Delivery" @selected(old('delivery_status',$pincode->delivery_status)==='Non-Delivery')>Non-Delivery</option>
                    </select>
                </div>
            </div>

            <label>City</label>
            <select name="city_id">
                <option value="">— Not mapped —</option>
                @foreach($cities as $c)
                    <option value="{{ $c->id }}" @selected(old('city_id',$pincode->city_id)==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>

            <label>Status</label>
            <select name="status">
                <option value="active" @selected(old('status',$pincode->status)==='active')>Active</option>
                <option value="inactive" @selected(old('status',$pincode->status)==='inactive')>Inactive</option>
            </select>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
@endsection
