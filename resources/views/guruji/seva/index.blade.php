@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.sv-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}
.sv-wrap{max-width:1060px;margin:0 auto;padding-bottom:40px}
.sv-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin:35px auto 22px;padding:34px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}
.sv-hero h1{margin:8px 0;font-size:32px;color:#fff}.sv-hero p{margin:0;color:#f8e4cc;font-size:14px}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.sv-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px}
.sv-card{border:1px solid #ede0d0;border-radius:16px;background:#fff;box-shadow:0 4px 12px #7a3a1006;overflow:hidden;transition:.15s}
.sv-card:hover{box-shadow:0 8px 22px #7a3a1010;transform:translateY(-2px)}
.sv-card-img{width:100%;height:140px;background:#fff4eb;display:flex;align-items:center;justify-content:center;color:#e8813a;font-size:40px;overflow:hidden}
.sv-card-img img{width:100%;height:100%;object-fit:cover}
.sv-card-body{padding:18px}
.sv-card-name{font-size:16px;font-weight:800;color:#2a1810;margin:0 0 6px}
.sv-card-desc{font-size:13px;color:#9a8070;margin:0 0 14px;line-height:1.5}
.sv-edit-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 16px;border-radius:10px;background:linear-gradient(100deg,#c4601a,#e8813a);color:#fff;font:700 13px inherit;text-decoration:none;transition:.15s}
.sv-edit-btn:hover{transform:translateY(-1px);box-shadow:0 6px 14px #e8813a25}
.flash-success{display:flex;align-items:center;gap:10px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.empty-state{padding:60px;text-align:center;border:1px dashed #e8c8a8;border-radius:16px;background:#fff}
</style>
<div class="dashboard sv-page">
    <div class="topbar">
        <div class="page-heading"><span>Seva & Donation</span><small>Manage your assigned donation categories</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    <div class="sv-wrap">
        @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

        <div class="sv-hero">
            <div>
                <span class="hero-overline">My Donations · {{ $guru->name }}</span>
                <h1>🙏 Seva & Donation</h1>
                <p>Update name, description and image for each donation category.</p>
            </div>
        </div>

        @if($categories->isEmpty())
            <div class="empty-state">
                <div style="font-size:40px;margin-bottom:12px">🙏</div>
                <h3 style="margin:0 0 8px;color:#2a1810">No categories assigned yet</h3>
                <p style="color:#9a8070;margin:0;font-size:14px">Ask your admin to assign donation categories to your profile.</p>
            </div>
        @else
            <div class="sv-grid">
                @foreach($categories as $cat)
                    <div class="sv-card">
                        <div class="sv-card-img">
                            @if($cat->image)<img src="{{ Storage::disk('public')->url($cat->image) }}" alt="">@else 🙏 @endif
                        </div>
                        <div class="sv-card-body">
                            <div class="sv-card-name">{{ $cat->name }}</div>
                            <div class="sv-card-desc">{{ \Illuminate\Support\Str::limit($cat->description, 80) ?: '—' }}</div>
                            <a href="{{ route('my.seva.edit', $cat) }}" class="sv-edit-btn">✎ Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
