@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.cities.index') }}">← Cities</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>
    <div class="booking-card" style="max-width:640px;margin:40px auto">
        <h1 style="font-size:28px;margin-bottom:6px">Add City</h1>
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('admin.location.cities.store') }}" id="city-form">
            @csrf
            <label>State / UT *</label>
            <select name="state_id" id="state-select" required onchange="loadDistricts(this.value)">
                <option value="">Select state</option>
                @foreach($states as $s)
                    <option value="{{ $s->id }}" @selected(old('state_id')==$s->id)>{{ $s->name }}</option>
                @endforeach
            </select>

            <label>District *</label>
            <select name="district_id" id="district-select" required>
                <option value="">Select state first</option>
            </select>

            <label>City Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="150">

            <label>Status</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit">Add City</button>
        </form>
    </div>
</div>
<script>
async function loadDistricts(stateId) {
    const sel = document.getElementById('district-select');
    sel.innerHTML = '<option value="">Loading…</option>';
    if (!stateId) { sel.innerHTML = '<option value="">Select state first</option>'; return; }
    const res = await fetch(`/api/locations/states/${stateId}/districts`);
    const data = await res.json();
    sel.innerHTML = '<option value="">Select district</option>' +
        data.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
}
</script>
@endsection
