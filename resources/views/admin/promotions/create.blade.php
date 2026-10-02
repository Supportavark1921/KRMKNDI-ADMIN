@extends('layouts.app')
@section('title', 'New Promotion')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">New Promotion</h1>
        <p class="store-sub"><a href="{{ route('admin.promotions.index') }}">App Content</a> / Create</p>
    </div>
</div>
<form method="POST" action="{{ route('admin.promotions.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.promotions._form')
</form>
@endsection
