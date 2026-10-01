@extends('layouts.app', ['title' => 'Add Mataji'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🕉 Add Mataji</span><small>Create a new Mataji / temple record</small></div>
    <a class="nav-link topbar-link" href="{{ route('admin.store.matajis.index') }}">← Back</a>
</header>

<div style="max-width:720px;margin:30px auto;padding:0 20px 80px">
@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif

<form method="POST" action="{{ route('admin.store.matajis.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.store.matajis._form')
    <button type="submit" class="btn-run" style="width:auto;padding:12px 32px">Save Mataji</button>
</form>
</div>
</main>
@endsection
