@extends('layouts.app', ['title' => 'My Profile - ARK Jyotish'])

@section('content')
<main class="dashboard">
    <header class="topbar">
        <div class="page-heading"><span>My profile</span><small>Birth details for Kundli and consultation</small></div>
        <a class="nav-link topbar-link" href="{{ route('dashboard') }}">Dashboard</a>
    </header>

    <section class="booking-card">
        <span class="pill">Member profile</span>
        <h1>{{ auth()->user()->name }}</h1>
        <p>Keep your birth details accurate so Pandit Ji can prepare the most precise Kundli reading for you.</p>

        @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PUT')

            <div class="form-grid">
                <div>
                    <label>Date of birth</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $profile->birth_date) }}">
                </div>
                <div>
                    <label>Time of birth</label>
                    <input type="time" name="birth_time" value="{{ old('birth_time', $profile->birth_time) }}">
                    <small class="field-help">24-hour format (e.g. 14:30)</small>
                </div>
            </div>

            <label>Place of birth</label>
            <input type="text" name="birth_place" maxlength="160" placeholder="City, State, Country"
                   value="{{ old('birth_place', $profile->birth_place) }}">

            <label>Address <em>(optional)</em></label>
            <textarea name="address" rows="3" maxlength="1000"
                      placeholder="Your current address…">{{ old('address', $profile->address) }}</textarea>

            <label>Family details <em>(optional)</em></label>
            <textarea name="family_details" rows="4" maxlength="1000"
                      placeholder="Spouse name, children, parents — any details relevant to your consultations…">{{ old('family_details', $profile->family_details) }}</textarea>

            <div style="display:flex;align-items:center;gap:16px">
                <button type="submit" style="width:auto;margin-top:24px;padding:13px 24px">Save profile</button>
                <a href="{{ route('dashboard') }}" style="margin-top:24px;color:#69758b;font-size:14px">Cancel</a>
            </div>
        </form>

        {{-- Account info --}}
        <div style="margin-top:36px;padding-top:28px;border-top:1px solid #e6e9f0">
            <h2 style="margin:0 0 16px;font-size:16px;color:#15233d">Account</h2>
            <div class="cards" style="grid-template-columns:repeat(2,1fr)">
                <div class="card"><b>Name</b><span>{{ auth()->user()->name }}</span></div>
                <div class="card"><b>Email</b><span>{{ auth()->user()->email }}</span></div>
                <div class="card"><b>Member since</b><span>{{ auth()->user()->created_at->format('d M Y') }}</span></div>
                <div class="card"><b>Role</b><span>Member</span></div>
            </div>
        </div>
    </section>
</main>
@endsection
