@extends('layouts.app')
@section('content')
<style>.don-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}.don-form-hero{max-width:860px;margin:32px auto 20px;padding:32px 38px;border-radius:20px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}.don-form-hero h1{margin:8px 0;font-size:30px;color:#fff}.don-form-hero p{max-width:520px;margin:0;color:#f8e4cc;line-height:1.6}.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}</style>
<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span><a href="{{ route('gurus.index') }}" style="color:inherit;text-decoration:none">Gurujis</a> › Add New</span></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    <div class="don-form-hero"><span class="hero-overline">Admin · Donations</span><h1>Add New Guruji</h1><p>Create a Guruji profile and assign donation categories for the mobile app.</p></div>
    @include('gurus._form', ['action' => route('gurus.store'), 'categories' => $categories, 'assigned' => []])
</div>
@endsection
