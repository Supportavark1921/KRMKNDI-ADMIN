@extends('layouts.app', ['title' => 'Edit Category'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>📦 Edit — {{ $category->name }}</span></div>
    <a class="nav-link topbar-link" href="{{ route('admin.store.categories.index') }}">← Back</a>
</header>
<div style="max-width:600px;margin:30px auto;padding:0 20px 80px">
@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('admin.store.categories.update', $category) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.store.categories._form')
    <button type="submit" class="btn-run" style="width:auto;padding:12px 32px">Update Category</button>
</form>
</div>
</main>
@endsection
