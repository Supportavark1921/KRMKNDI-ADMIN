@extends('layouts.app')
@section('content')
<style>
.svc-form-page{background:radial-gradient(circle at 90% 4%,#e6e0ff 0,transparent 22%),#f6f7fc!important}
.svc-form-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:960px;margin:35px auto 22px;padding:34px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#9d80ef 0,transparent 26%),linear-gradient(118deg,#1a1442,#3a2d82)}
.svc-form-hero h1{margin:8px 0;font-size:32px;color:#fff}
.svc-form-hero p{max-width:540px;margin:0;color:#d8d4f8;line-height:1.6}
.hero-overline{color:#ffdc87;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
</style>

<div class="dashboard svc-form-page">
    <div class="topbar">
        <div class="page-heading">
            <span><a href="{{ route('services.index') }}" style="color:inherit;text-decoration:none">Services</a> › Edit</span>
            <small>{{ $service->name() }}</small>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="svc-form-hero">
        <div>
            <span class="hero-overline">Admin · Services</span>
            <h1>Edit Service</h1>
            <p>Update content, translations, images, and pricing. Changes are reflected in the API immediately.</p>
        </div>
    </div>

    @include('services._form', [
        'service'   => $service,
        'action'    => route('services.update', $service),
        'method'    => 'PUT',
        'languages' => $languages,
    ])
</div>
@endsection
