@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.don-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}
.don-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1100px;margin:32px auto 20px;padding:34px 42px;border-radius:22px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}
.don-hero h1{margin:8px 0;font-size:34px;color:#fff}.don-hero p{max-width:560px;margin:0;color:#f8e4cc;line-height:1.6}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.hero-add-btn{display:flex;align-items:center;gap:8px;flex:0 0 auto;padding:12px 16px;border-radius:12px;color:#4a1f05;background:linear-gradient(120deg,#ffdd7c,#f0a030);font-size:13px;font-weight:800;white-space:nowrap;text-decoration:none;box-shadow:0 10px 22px #7a3a1033}
.cat-board{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;max-width:1100px;margin:0 auto}
.cat-card{padding:20px;border:1px solid #ede0d0;border-radius:16px;background:#fff;box-shadow:0 4px 12px #7a3a1006;transition:.15s}
.cat-card:hover{box-shadow:0 8px 22px #7a3a1010;transform:translateY(-2px)}
.cat-card-img{width:52px;height:52px;border-radius:13px;background:#fff4eb;border:1px solid #f0d0a8;display:flex;align-items:center;justify-content:center;color:#e8813a;font-size:22px;overflow:hidden;margin-bottom:14px}
.cat-card-img img{width:100%;height:100%;object-fit:cover;border-radius:13px}
.cat-card h3{margin:0 0 6px;font-size:16px;color:#2a1810}
.cat-card p{margin:0 0 14px;color:#9a8070;font-size:13px;line-height:1.5}
.cat-card-footer{display:flex;align-items:center;justify-content:space-between}
.cat-meta{font-size:12px;color:#b09080}
.status-active{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.status-inactive{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:11px;font-weight:750}
.status-active::before,.status-inactive::before{content:"";width:5px;height:5px;border-radius:50%}
.status-active::before{background:#28a76a}.status-inactive::before{background:#e69c3a}
.cat-actions{display:flex;gap:6px}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;border:1px solid #ede0d0;background:#fff;color:#7a6050;font-size:13px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#e8813a;color:#e8813a;background:#fff4eb}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1100px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.flash-error{display:flex;align-items:center;gap:10px;max-width:1100px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fff0f0;color:#b33;font-size:14px;font-weight:600}
.pag-wrap{max-width:1100px;margin:20px auto 0;display:flex;justify-content:center}
.pag-wrap .pagination{display:flex;gap:6px;list-style:none;margin:0;padding:0}
.pag-wrap .page-item .page-link{display:flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;border:1px solid #ede0d0;border-radius:9px;color:#7a6050;font-size:13px;font-weight:600;text-decoration:none;transition:.15s}
.pag-wrap .page-item.active .page-link{background:#e8813a;border-color:#e8813a;color:#fff}
</style>
<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span>Donations › Categories</span><small>Manage donation purposes</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error">⚠ {{ session('error') }}</div>@endif

    <div class="don-hero">
        <div><span class="hero-overline">Admin · Donations</span><h1>Donation Categories</h1><p>Categories represent the purpose for which a Guruji receives donations (Ann Prasadhan, Gau Seva, etc.).</p></div>
        @can('donation-categories.create')
        <a href="{{ route('donation-categories.create') }}" class="hero-add-btn">＋ Add Category</a>
        @endcan
    </div>

    @if($categories->isEmpty())
        <div style="max-width:1100px;margin:0 auto;padding:60px;text-align:center;border:1px dashed #e8c8a8;border-radius:16px;background:#fff">
            <div style="font-size:40px;margin-bottom:12px">❧</div>
            <h3 style="margin:0 0 8px;color:#2a1810">No categories yet</h3>
            <p style="color:#9a8070;margin:0 0 20px;font-size:14px">Create your first donation category.</p>
            <a href="{{ route('donation-categories.create') }}" style="padding:12px 18px;border-radius:11px;background:#e8813a;color:#fff;font:700 14px inherit;text-decoration:none">＋ Add Category</a>
        </div>
    @else
        <div class="cat-board">
            @foreach($categories as $cat)
                <div class="cat-card">
                    <div class="cat-card-img">
                        @if($cat->image)<img src="{{ Storage::disk('public')->url($cat->image) }}" alt="">@else ❧ @endif
                    </div>
                    <h3>{{ $cat->name }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit($cat->description, 80) ?: '—' }}</p>
                    <div class="cat-card-footer">
                        <div>
                            <span class="status-{{ $cat->status }}">{{ ucfirst($cat->status) }}</span>
                            <div class="cat-meta" style="margin-top:5px">{{ $cat->gurus_count }} Guruji · {{ $cat->donations_count }} donations</div>
                        </div>
                        <div class="cat-actions">
                            @if(auth()->user()->can('donation-categories.update'))
                                <a href="{{ route('donation-categories.edit', $cat) }}" class="btn-icon" title="Edit">✎</a>
                            @elseif(isset($myGuruId) && $myGuruId && $cat->gurus->contains('id', $myGuruId))
                                <a href="{{ route('donation-categories.my-edit', $cat) }}" class="btn-icon" title="Edit My Content" style="color:#e8813a;border-color:#e8c8a8">✎</a>
                            @endif
                            @can('donation-categories.delete')
                            <form method="POST" action="{{ route('donation-categories.destroy', $cat) }}" onsubmit="return confirm('Delete {{ $cat->name }}? Cannot delete if donations exist.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon danger" title="Delete">🗑</button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($categories->hasPages())
            <div class="pag-wrap">{{ $categories->links() }}</div>
        @endif
    @endif
</div>
@endsection
