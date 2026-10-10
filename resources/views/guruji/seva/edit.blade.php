@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.sv-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}
.sv-wrap{max-width:760px;margin:0 auto;padding-bottom:40px}
.sv-hero{display:flex;align-items:center;gap:24px;margin:35px auto 22px;padding:30px 36px;border-radius:20px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}
.sv-hero h1{margin:6px 0 4px;font-size:26px;color:#fff}.sv-hero p{margin:0;color:#f8e4cc;font-size:13px}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.sv-section{padding:24px 28px;border:1px solid #ede0d0;border-radius:16px;background:#fff;margin-bottom:16px;box-shadow:0 4px 12px #7a3a1006}
.sv-sec-title{font-size:13px;font-weight:800;color:#7a6050;letter-spacing:.06em;text-transform:uppercase;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f5ece0}
.sv-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#4a3020}
.sv-input,.sv-textarea{width:100%;padding:11px 13px;border:1px solid #e8ddd0;border-radius:10px;font:inherit;color:#2a1810;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.sv-input:focus,.sv-textarea:focus{border-color:#e8813a;box-shadow:0 0 0 3px #fff4eb}
.sv-textarea{resize:vertical;min-height:80px}
.sv-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}
.img-drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border:2px dashed #e8c8a8;border-radius:12px;padding:18px;background:#fffaf5;cursor:pointer;transition:.2s;text-align:center}
.img-drop:hover{border-color:#e8813a;background:#fff4eb}
.img-preview-sq{width:100px;height:100px;border-radius:12px;overflow:hidden;border:2px solid #f0d0a8;display:none;position:relative}
.img-preview-sq img{width:100%;height:100%;object-fit:cover}
.img-preview-rm{position:absolute;top:4px;right:4px;display:grid;width:20px;height:20px;place-items:center;border-radius:50%;background:#1b2240bb;color:#fff;cursor:pointer;font-size:10px;border:0}
.sv-actions{display:flex;gap:12px;justify-content:flex-end;padding:20px 0 0}
.sv-btn-primary{padding:12px 24px;border-radius:11px;border:0;color:#fff;background:linear-gradient(100deg,#c4601a,#e8813a);font:700 14px inherit;cursor:pointer;box-shadow:0 8px 18px #e8813a25;transition:.2s}
.sv-btn-primary:hover{transform:translateY(-1px)}
.sv-btn-back{padding:12px 16px;border-radius:11px;border:1px solid #ede0d0;color:#7a6050;background:#fff;font:600 14px inherit;text-decoration:none;display:inline-flex;align-items:center;transition:.15s}
.sv-btn-back:hover{background:#fffaf5}
.flash-success{display:flex;align-items:center;gap:10px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
</style>
<div class="dashboard sv-page">
    <div class="topbar">
        <div class="page-heading"><span>Seva & Donation › Edit</span><small>{{ $donationCategory->name }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    <div class="sv-wrap">
        @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

        <div class="sv-hero">
            <div>
                <span class="hero-overline">Edit · Donation Category</span>
                <h1>{{ $donationCategory->name }}</h1>
                <p>Update name, description and image shown in the app.</p>
            </div>
        </div>

        @if($errors->any())
            <div style="padding:13px 16px;border-radius:12px;background:#fff0f0;border:1px solid #fdc9c9;margin-bottom:16px;color:#b33;font-size:13px">
                <strong>Please fix:</strong>
                <ul style="margin:8px 0 0;padding-left:20px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('my.seva.update', $donationCategory) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="sv-section">
                <div class="sv-sec-title">① Name & Description</div>
                <div style="margin-bottom:14px">
                    <label class="sv-label">Category Name <span style="color:#e04a4a">*</span></label>
                    <input type="text" name="name" class="sv-input"
                           value="{{ old('name', $donationCategory->name) }}" required
                           placeholder="e.g. Ann Prasadhan">
                    @error('name')<span class="sv-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="sv-label">Description</label>
                    <textarea name="description" class="sv-textarea" rows="4"
                              placeholder="What this donation supports…">{{ old('description', $donationCategory->description) }}</textarea>
                    @error('description')<span class="sv-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="sv-section">
                <div class="sv-sec-title">② Image / Icon</div>
                <div style="display:flex;align-items:flex-start;gap:20px">
                    <div id="cat-preview" class="img-preview-sq" style="{{ $donationCategory->image ? 'display:block' : '' }}">
                        <img src="{{ $donationCategory->image ? Storage::disk('public')->url($donationCategory->image) : '' }}" id="cat-preview-img" alt="">
                        <button type="button" class="img-preview-rm" onclick="removeCatImg()">×</button>
                    </div>
                    <div class="img-drop" id="cat-drop" onclick="document.getElementById('cat-img').click()" style="{{ $donationCategory->image ? 'display:none' : '' }}">
                        <span style="font-size:24px;color:#e8813a">🖼</span>
                        <p style="margin:0;color:#9a8070;font-size:13px">Click to upload image</p>
                        <small style="color:#c0a888;font-size:11px">JPG, PNG · max 5 MB</small>
                    </div>
                    <input type="file" id="cat-img" name="image" accept="image/*" style="display:none" onchange="previewCatImg(this)">
                </div>
                @error('image')<span class="sv-error" style="display:block;margin-top:8px">{{ $message }}</span>@enderror
            </div>

            <div class="sv-actions">
                <a href="{{ route('my.seva.index') }}" class="sv-btn-back">← Back</a>
                <button type="submit" class="sv-btn-primary">💾 Save Changes</button>
            </div>
        </form>
    </div>
</div>
<script>
function previewCatImg(input) {
    if (!input.files[0]) return;
    document.getElementById('cat-preview-img').src = URL.createObjectURL(input.files[0]);
    document.getElementById('cat-preview').style.display = 'block';
    document.getElementById('cat-drop').style.display = 'none';
}
function removeCatImg() {
    document.getElementById('cat-preview').style.display = 'none';
    document.getElementById('cat-drop').style.display = 'flex';
    document.getElementById('cat-img').value = '';
}
</script>
@endsection
