@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.ms-page{background:radial-gradient(circle at 90% 4%,#fff4e0 0,transparent 22%),#f8f7f2!important}
.ms-wrap{max-width:1060px;margin:0 auto;padding-bottom:40px}
.ms-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin:35px auto 22px;padding:32px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#f0b060 0,transparent 26%),linear-gradient(118deg,#1a0f02,#6b3a08)}
.ms-hero h1{margin:8px 0;font-size:30px;color:#fff}.ms-hero p{margin:0;color:#f8e8cc;font-size:14px}
.hero-overline{color:#ffe085;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.ms-add-btn{display:flex;align-items:center;gap:8px;padding:12px 16px;border-radius:12px;color:#3a1f02;background:linear-gradient(120deg,#ffdd7c,#f0a030);font:800 13px inherit;text-decoration:none;white-space:nowrap;box-shadow:0 8px 18px #6b3a0822}
.ms-table-wrap{background:#fff;border:1px solid #ede8e0;border-radius:16px;overflow:hidden;box-shadow:0 4px 12px #6b3a0806}
table.ms-table{width:100%;border-collapse:collapse}
.ms-table th{background:#faf7f2;padding:12px 16px;text-align:left;font-size:11px;font-weight:800;color:#7a6050;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #ede8e0}
.ms-table td{padding:13px 16px;border-bottom:1px solid #f5f0e8;font-size:13px;color:#2a1810;vertical-align:middle}
.ms-table tr:last-child td{border-bottom:0}
.ms-table tr:hover td{background:#fffaf5}
.status-active{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.status-inactive{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:11px;font-weight:750}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;border:1px solid #ede8e0;background:#fff;color:#7a6050;font-size:13px;cursor:pointer;text-decoration:none;transition:.15s}
.btn-icon:hover{border-color:#e8813a;color:#e8813a;background:#fff4eb}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.flash-success{display:flex;align-items:center;gap:10px;margin:0 0 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.flash-error{display:flex;align-items:center;gap:10px;margin:0 0 16px;padding:13px 16px;border-radius:12px;background:#fff0f0;color:#b33;font-size:14px;font-weight:600}
.cat-img{width:36px;height:36px;border-radius:8px;object-fit:cover;border:1px solid #ede8e0}
</style>
<div class="dashboard ms-page">
    <div class="topbar">
        <div class="page-heading"><span>My Store › Categories</span><small>Manage your product categories</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    <div class="ms-wrap">
        @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif
        @if(session('error'))<div class="flash-error">⚠ {{ session('error') }}</div>@endif

        <div class="ms-hero">
            <div>
                <span class="hero-overline">My Store · {{ $guru->name }}</span>
                <h1>📦 Product Categories</h1>
                <p>Create categories to organise your products.</p>
            </div>
            @can('my-store.create')
            <a href="{{ route('my.store.categories.create') }}" class="ms-add-btn">＋ Add Category</a>
            @endcan
        </div>

        @if($categories->isEmpty())
            <div style="padding:60px;text-align:center;border:1px dashed #e8c8a8;border-radius:16px;background:#fff">
                <div style="font-size:36px;margin-bottom:12px">📦</div>
                <h3 style="margin:0 0 8px;color:#2a1810">No categories yet</h3>
                <p style="color:#9a8070;margin:0 0 20px;font-size:14px">Create your first category, then add products under it.</p>
                <a href="{{ route('my.store.categories.create') }}" style="padding:11px 18px;border-radius:10px;background:#e8813a;color:#fff;font:700 14px inherit;text-decoration:none">＋ Add Category</a>
            </div>
        @else
            <div class="ms-table-wrap">
                <table class="ms-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                        <tr>
                            <td>
                                @if($cat->image)
                                    <img src="{{ Storage::disk('public')->url($cat->image) }}" class="cat-img" alt="">
                                @else
                                    <div style="width:36px;height:36px;border-radius:8px;background:#fff4eb;display:flex;align-items:center;justify-content:center;font-size:16px">📦</div>
                                @endif
                            </td>
                            <td style="font-weight:700">{{ $cat->name }}</td>
                            <td style="color:#9a8070">{{ $cat->products_count }}</td>
                            <td><span class="status-{{ $cat->status }}">{{ ucfirst($cat->status) }}</span></td>
                            <td>
                                <div style="display:flex;gap:6px">
                                    <a href="{{ route('my.store.categories.edit', $cat) }}" class="btn-icon" title="Edit">✎</a>
                                    <form method="POST" action="{{ route('my.store.categories.destroy', $cat) }}" onsubmit="return confirm('Delete {{ $cat->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Delete">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
