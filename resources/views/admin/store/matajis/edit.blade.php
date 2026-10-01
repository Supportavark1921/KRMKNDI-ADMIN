@extends('layouts.app', ['title' => 'Edit Mataji'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🕉 Edit — {{ $mataji->name }}</span><small>Update Mataji details</small></div>
    <a class="nav-link topbar-link" href="{{ route('admin.store.matajis.index') }}">← Back</a>
</header>

<div style="max-width:720px;margin:30px auto;padding:0 20px 80px">
@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif

<form method="POST" action="{{ route('admin.store.matajis.update', $mataji) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.store.matajis._form')
    <button type="submit" class="btn-run" style="width:auto;padding:12px 32px">Update Mataji</button>
</form>
</div>
</main>
@endsection
