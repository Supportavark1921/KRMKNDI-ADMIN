@extends('layouts.app')
@section('title', 'New Mataji Order')
@section('content')
<div class="store-header">
    <h1 class="store-title">New Mataji Order</h1>
</div>

@if($errors->any())
<div class="alert-error" style="margin-bottom:12px">
    <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.mataji-orders.store') }}">
    @csrf
    @include('admin.mataji-orders._form')
</form>
@endsection
