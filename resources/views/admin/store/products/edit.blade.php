@extends('layouts.app', ['title' => 'Edit Product'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🛍 Edit — {{ $product->name }}</span><small>{{ $product->product_code }}</small></div>
    <div style="display:flex;gap:10px">
        <a class="nav-link topbar-link" href="{{ route('admin.store.products.show', $product) }}">View</a>
        <a class="nav-link topbar-link" href="{{ route('admin.store.products.index') }}">← Back</a>
    </div>
</header>
<div style="max-width:820px;margin:30px auto;padding:0 20px 80px">
@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('admin.store.products.update', $product) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.store.products._form')
    <button type="submit" class="btn-run" style="width:auto;padding:12px 32px">Update Product</button>
</form>
</div>
</main>
@endsection
