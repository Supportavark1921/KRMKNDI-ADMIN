@extends('layouts.app')
@section('title', 'Edit Promotion')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">Edit: {{ $promotion->title }}</h1>
        <p class="store-sub"><a href="{{ route('admin.promotions.index') }}">App Content</a> / Edit</p>
    </div>
</div>
<form method="POST" action="{{ route('admin.promotions.update', $promotion) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.promotions._form')
</form>
@endsection
