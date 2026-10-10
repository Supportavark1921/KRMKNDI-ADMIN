@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.pj-page{background:radial-gradient(circle at 90% 4%,#e6e0ff 0,transparent 22%),#f6f7fc!important}
.pj-wrap{max-width:1060px;margin:0 auto;padding-bottom:40px}
.pj-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin:35px auto 22px;padding:34px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#9d80ef 0,transparent 26%),linear-gradient(118deg,#1a1442,#3a2d82)}
.pj-hero h1{margin:8px 0;font-size:32px;color:#fff}.pj-hero p{margin:0;color:#d8d4f8;font-size:14px}
.hero-overline{color:#ffdc87;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.pj-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px}
.pj-card{border:1px solid #e4e7f2;border-radius:16px;background:#fff;box-shadow:0 4px 14px #1e2a5a06;overflow:hidden;transition:.15s}
.pj-card:hover{box-shadow:0 8px 24px #1e2a5a10;transform:translateY(-2px)}
.pj-card-img{width:100%;height:160px;object-fit:cover;background:#ede9ff;display:flex;align-items:center;justify-content:center;color:#8b78d8;font-size:40px}
.pj-card-img img{width:100%;height:100%;object-fit:cover}
.pj-card-body{padding:18px}
.pj-card-name{font-size:16px;font-weight:800;color:#1b2240;margin:0 0 6px}
.pj-card-desc{font-size:13px;color:#7a8299;margin:0 0 14px;line-height:1.5}
.pj-price{font-size:18px;font-weight:800;color:#3a2d82;margin:0 0 4px}
.pj-price small{font-size:12px;font-weight:600;color:#9aa3bc;text-decoration:line-through;margin-left:6px}
.pj-samagri-count{font-size:12px;color:#9aa3bc;margin:0 0 14px}
.pj-edit-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 16px;border-radius:10px;background:linear-gradient(100deg,#3a2d82,#6b5cc8);color:#fff;font:700 13px inherit;text-decoration:none;transition:.15s}
.pj-edit-btn:hover{transform:translateY(-1px);box-shadow:0 6px 14px #6b5cc825}
.flash-success{display:flex;align-items:center;gap:10px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.empty-state{padding:60px;text-align:center;border:1px dashed #d4d0f0;border-radius:16px;background:#fff}
</style>
<div class="dashboard pj-page">
    <div class="topbar">
        <div class="page-heading"><span>Poojan Services</span><small>Manage your assigned pooja services</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    <div class="pj-wrap">
        @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

        <div class="pj-hero">
            <div>
                <span class="hero-overline">My Services · {{ $guru->name }}</span>
                <h1>🪔 Poojan</h1>
                <p>Update name, description, images, samagri list and price for each service.</p>
            </div>
        </div>

        @if($services->isEmpty())
            <div class="empty-state">
                <div style="font-size:40px;margin-bottom:12px">🪔</div>
                <h3 style="margin:0 0 8px;color:#1b2240">No services assigned yet</h3>
                <p style="color:#9aa3bc;margin:0;font-size:14px">Ask your admin to assign services to your Guruji profile.</p>
            </div>
        @else
            <div class="pj-grid">
                @foreach($services as $service)
                    @php $gs = $service->guruServices->first(); @endphp
                    <div class="pj-card">
                        <div class="pj-card-img">
                            @if($service->primaryImage())
                                <img src="{{ Storage::disk('public')->url($service->primaryImage()) }}" alt="">
                            @else
                                🪔
                            @endif
                        </div>
                        <div class="pj-card-body">
                            <div class="pj-card-name">{{ $service->translate('name') }}</div>
                            <div class="pj-card-desc">{{ \Illuminate\Support\Str::limit($service->translate('title') ?: $service->translate('description'), 80) ?: '—' }}</div>
                            @if($gs)
                                <div class="pj-price">
                                    {{ $gs->currency() === 'INR' ? '₹' : $gs->currency() }}{{ number_format($gs->amount()) }}
                                    @if($gs->discountAmount())
                                        <small>{{ $gs->currency() === 'INR' ? '₹' : $gs->currency() }}{{ number_format($gs->discountAmount()) }}</small>
                                    @endif
                                </div>
                                <div class="pj-samagri-count">{{ count($gs->samagri()) }} samagri item(s)</div>
                            @endif
                            <a href="{{ route('my.poojan.edit', $service) }}" class="pj-edit-btn">✎ Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
