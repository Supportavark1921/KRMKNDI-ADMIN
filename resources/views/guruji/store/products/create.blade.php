@extends('layouts.app')
@section('content')
<style>.ms-page{background:radial-gradient(circle at 90% 4%,#fff4e0 0,transparent 22%),#f8f7f2!important}</style>
<div class="dashboard ms-page">
    <div class="topbar">
        <div class="page-heading"><span>My Store › Add Product</span></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    @if($categories->isEmpty())
        <div style="max-width:800px;margin:40px auto;padding:40px;text-align:center;border:1px dashed #e8c8a8;border-radius:16px;background:#fff">
            <div style="font-size:32px;margin-bottom:12px">📦</div>
            <h3 style="margin:0 0 8px;color:#2a1810">Create a category first</h3>
            <p style="color:#9a8070;margin:0 0 20px;font-size:14px">Products must belong to a category. Add one before creating products.</p>
            <a href="{{ route('my.store.categories.create') }}" style="padding:11px 18px;border-radius:10px;background:#e8813a;color:#fff;font:700 14px inherit;text-decoration:none">＋ Add Category</a>
        </div>
    @else
        @include('guruji.store.products._form', ['action' => route('my.store.products.store')])
    @endif
</div>
@endsection
