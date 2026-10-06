@extends('layouts.app', ['title' => 'Product Categories'])
@section('content')
<style>
.cat-page { max-width: 960px; margin: 0 auto; padding: 0 20px 80px; }
.cat-hero { background: linear-gradient(135deg,#1a3a2a 0%,#2d6a4f 100%); border-radius: 18px; padding: 28px 32px; margin-bottom: 28px; color: #fff; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.cat-hero h1 { font-size: 22px; font-weight: 800; margin: 0 0 4px; }
.cat-hero small { opacity: .7; font-size: 13px; }
.cat-add-btn { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25); color: #fff; padding: 9px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; white-space: nowrap; }
.cat-add-btn:hover { background: rgba(255,255,255,.25); }
.cat-card { background: #fff; border: 1px solid #e6e8f0; border-radius: 16px; margin-bottom: 16px; overflow: hidden; }
.cat-parent-row { display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: #f7f9fb; border-bottom: 1px solid #e6e8f0; }
.cat-icon { width: 38px; height: 38px; border-radius: 10px; object-fit: cover; }
.cat-icon-placeholder { width: 38px; height: 38px; border-radius: 10px; background: #e8f4ee; display: grid; place-items: center; font-size: 18px; }
.cat-parent-name { font-weight: 800; font-size: 15px; color: #15233d; flex: 1; }
.cat-badge { font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; }
.badge-active { background: #e4f6ea; color: #276946; }
.badge-inactive { background: #fdeaea; color: #c0392b; }
.cat-actions a, .cat-actions button { font-size: 12px; font-weight: 700; margin-left: 10px; cursor: pointer; background: none; border: none; padding: 0; }
.cat-sub-row { display: flex; align-items: center; gap: 14px; padding: 10px 18px 10px 52px; border-bottom: 1px solid #f3f5f9; }
.cat-sub-row:last-child { border-bottom: none; }
.cat-sub-name { color: #374151; font-size: 13px; font-weight: 600; flex: 1; }
.cat-sub-meta { font-size: 12px; color: #8a9ab8; }
.notice { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; font-weight: 600; }
.notice.success { background: #e4f6ea; color: #276946; }
.notice.error   { background: #fdeaea; color: #c0392b; }
</style>

<div class="dashboard cat-page" style="padding-top:24px">
<header class="topbar">
    <div class="page-heading"><span>Categories</span><small>Product category tree</small></div>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.store.categories.create') }}" class="cat-add-btn" style="background:#2d6a4f;border:none">+ Add Category</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
</header>

<div class="cat-hero">
    <div>
        <h1>Product Categories</h1>
        <small>{{ $roots->count() }} parent categories · {{ $roots->sum(fn($r) => $r->children->count()) }} subcategories</small>
    </div>
    <a href="{{ route('admin.store.categories.create') }}" class="cat-add-btn">+ Add Category</a>
</div>

@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="notice error">{{ session('error') }}</div>@endif

@forelse($roots as $parent)
<div class="cat-card">
    {{-- Parent row --}}
    <div class="cat-parent-row">
        @if($parent->image)
            <img src="{{ Storage::url($parent->image) }}" class="cat-icon">
        @else
            <div class="cat-icon-placeholder">📦</div>
        @endif
        <span class="cat-parent-name">{{ $parent->name }}</span>
        <span class="cat-sub-meta">{{ $parent->children->count() }} sub · {{ $parent->products_count }} direct products</span>
        <span class="cat-badge {{ $parent->status === 'active' ? 'badge-active' : 'badge-inactive' }}">{{ ucfirst($parent->status) }}</span>
        <div class="cat-actions">
            <a href="{{ route('admin.store.categories.create') }}?parent={{ $parent->id }}" style="color:#2d6a4f">+ Sub</a>
            <a href="{{ route('admin.store.categories.edit', $parent) }}" style="color:#5c4bb7">Edit</a>
            <form method="POST" action="{{ route('admin.store.categories.destroy', $parent) }}" style="display:inline" onsubmit="return confirm('Delete this category?')">
                @csrf @method('DELETE')
                <button type="submit" style="color:#c0392b">Delete</button>
            </form>
        </div>
    </div>

    {{-- Subcategory rows --}}
    @foreach($parent->children as $sub)
    <div class="cat-sub-row">
        <span style="color:#bbb;font-size:16px">└</span>
        @if($sub->image)
            <img src="{{ Storage::url($sub->image) }}" class="cat-icon" style="width:28px;height:28px">
        @else
            <div class="cat-icon-placeholder" style="width:28px;height:28px;font-size:14px;background:#f3f5f9">📂</div>
        @endif
        <span class="cat-sub-name">{{ $sub->name }}</span>
        <span class="cat-sub-meta">{{ $sub->products->count() }} products</span>
        <span class="cat-badge {{ $sub->status === 'active' ? 'badge-active' : 'badge-inactive' }}">{{ ucfirst($sub->status) }}</span>
        <div class="cat-actions">
            <a href="{{ route('admin.store.categories.edit', $sub) }}" style="color:#5c4bb7">Edit</a>
            <form method="POST" action="{{ route('admin.store.categories.destroy', $sub) }}" style="display:inline" onsubmit="return confirm('Delete this subcategory?')">
                @csrf @method('DELETE')
                <button type="submit" style="color:#c0392b">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@empty
    <div style="text-align:center;padding:60px;color:#8a9ab8;background:#fff;border-radius:16px;border:1px solid #e6e8f0">
        No categories yet. <a href="{{ route('admin.store.categories.create') }}" style="color:#2d6a4f;font-weight:700">Add one</a>
    </div>
@endforelse
</div>
@endsection
