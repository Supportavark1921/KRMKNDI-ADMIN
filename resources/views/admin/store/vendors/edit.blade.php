@extends('layouts.app', ['title' => 'Edit Vendor'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🏪 Edit — {{ $vendor->business_name }}</span></div>
    <a class="nav-link topbar-link" href="{{ route('admin.store.vendors.show', $vendor) }}">← Back</a>
</header>
<div style="max-width:720px;margin:30px auto;padding:0 20px 80px">
@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('admin.store.vendors.update', $vendor) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.store.vendors._form')
    <button type="submit" class="btn-run" style="width:auto;padding:12px 32px">Update Vendor</button>
</form>
</div>
</main>
@endsection
