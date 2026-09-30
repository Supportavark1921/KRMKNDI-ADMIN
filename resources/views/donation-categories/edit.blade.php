@extends('layouts.app')
@section('content')
<style>.don-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}.don-form-hero{max-width:760px;margin:32px auto 20px;padding:30px 36px;border-radius:18px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}.don-form-hero h1{margin:8px 0;font-size:28px;color:#fff}.don-form-hero p{margin:0;color:#f8e4cc;line-height:1.6;font-size:13px}.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}</style>
<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span><a href="{{ route('donation-categories.index') }}" style="color:inherit;text-decoration:none">Categories</a> › Edit</span><small>{{ $category->name }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    <div class="don-form-hero"><span class="hero-overline">Admin · Donations</span><h1>Edit Category</h1><p>Update {{ $category->name }} details and Guruji assignments.</p></div>
    @include('donation-categories._form', ['action' => route('donation-categories.update', $category), 'method' => 'PUT', 'category' => $category, 'gurus' => $gurus, 'assigned' => $assigned])
</div>
@endsection
