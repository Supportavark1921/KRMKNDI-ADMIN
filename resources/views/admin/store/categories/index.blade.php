@extends('layouts.app', ['title' => 'Product Categories'])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>📦 Categories</span><small>Product categories for the store</small></div>
    <div style="display:flex;gap:10px">
        <a class="nav-link topbar-link" href="{{ route('admin.store.categories.create') }}" style="background:#f0ecff;color:#5c4bb7">+ Add Category</a>
        <a class="nav-link topbar-link" href="{{ route('dashboard') }}">Dashboard</a>
    </div>
</header>

<div style="max-width:900px;margin:30px auto;padding:0 20px 80px">
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="notice error">{{ session('error') }}</div>@endif

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;overflow:hidden">
<table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead>
        <tr style="background:#f9f9fb;border-bottom:1px solid #e6e8f0">
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Category</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Slug</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Products</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Status</th>
            <th style="padding:12px 16px"></th>
        </tr>
    </thead>
    <tbody>
    @forelse($categories as $cat)
        <tr style="border-bottom:1px solid #f0f2f8">
            <td style="padding:13px 16px">
                <div style="display:flex;align-items:center;gap:10px">
                    @if($cat->image)
                        <img src="{{ asset('storage/'.$cat->image) }}" style="width:34px;height:34px;border-radius:8px;object-fit:cover">
                    @else
                        <div style="width:34px;height:34px;border-radius:8px;background:#f3f5f9;display:grid;place-items:center">📦</div>
                    @endif
                    <b style="color:#15233d">{{ $cat->name }}</b>
                </div>
            </td>
            <td style="padding:13px 16px;font-family:monospace;color:#8a9ab8;font-size:12px">{{ $cat->slug }}</td>
            <td style="padding:13px 16px;text-align:center;color:#15233d;font-weight:700">{{ $cat->products_count }}</td>
            <td style="padding:13px 16px">
                <span style="padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;{{ $cat->status === 'active' ? 'background:#e4f6ea;color:#276946' : 'background:#fdeaea;color:#c0392b' }}">
                    {{ ucfirst($cat->status) }}
                </span>
            </td>
            <td style="padding:13px 16px;text-align:right">
                <a href="{{ route('admin.store.categories.edit', $cat) }}" style="color:#5c4bb7;font-weight:700;font-size:12px;margin-right:12px">Edit</a>
                <form method="POST" action="{{ route('admin.store.categories.destroy', $cat) }}" style="display:inline" onsubmit="return confirm('Delete this category?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="border:none;background:none;color:#c0392b;font-weight:700;font-size:12px;cursor:pointer">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" style="padding:40px;text-align:center;color:#8a9ab8">No categories yet.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div style="margin-top:16px">{{ $categories->links() }}</div>
</div>
</main>
@endsection
