@extends('layouts.app')
@section('content')
<style>.ms-page{background:radial-gradient(circle at 90% 4%,#fff4e0 0,transparent 22%),#f8f7f2!important}</style>
<div class="dashboard ms-page">
    <div class="topbar">
        <div class="page-heading"><span>My Store › Add Category</span></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    @include('guruji.store.categories._form', ['action' => route('my.store.categories.store')])
</div>
@endsection
