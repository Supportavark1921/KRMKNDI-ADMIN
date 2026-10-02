@extends('layouts.app')
@section('title', 'Edit Order #' . $order->id)
@section('content')
<div class="store-header">
    <h1 class="store-title">Edit Draft Order #{{ $order->id }}</h1>
</div>

@if($errors->any())
<div class="alert-error" style="margin-bottom:12px">
    <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.mataji-orders.update', $order) }}">
    @csrf
    @method('PUT')
    @include('admin.mataji-orders._form')
</form>
@endsection
