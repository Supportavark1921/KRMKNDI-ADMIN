@extends('layouts.app', ['title' => 'Add Category'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>📦 Add Category</span></div>
    <a class="nav-link topbar-link" href="{{ route('admin.store.categories.index') }}">← Back</a>
</header>
<div style="max-width:600px;margin:30px auto;padding:0 20px 80px">
@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('admin.store.categories.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.store.categories._form')
    <button type="submit" class="btn-run" style="width:auto;padding:12px 32px">Save Category</button>
</form>
</div>
</main>
@endsection
