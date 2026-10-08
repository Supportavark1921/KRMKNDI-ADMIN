@extends('layouts.app')
@section('title', 'Edit Article')
@section('content')
<style>
.art-form-page{background:radial-gradient(circle at 92% 4%,#e8f4ff 0,transparent 22%),#f5f9ff!important}
.art-form-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:960px;margin:35px auto 28px;padding:32px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#60a5fa 0,transparent 26%),linear-gradient(118deg,#0c2461,#1a56db)}
.art-form-hero h1{margin:6px 0 0;font-size:28px;color:#fff}
.art-form-hero p{margin:5px 0 0;color:#bfdbfe;font-size:14px}
.art-overline{color:#fde68a;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.art-form-card{max-width:960px;margin:0 auto;border:1px solid #dbeafe;border-radius:20px;background:#fff;box-shadow:0 18px 45px #0c246109;overflow:hidden}
.art-form-section{padding:26px 32px;border-bottom:1px solid #eff6ff}
.art-form-section:last-child{border-bottom:0}
.art-section-title{font-size:12px;font-weight:800;color:#1e40af;letter-spacing:.07em;text-transform:uppercase;margin:0 0 16px}
.art-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.art-field{display:flex;flex-direction:column;gap:6px}
.art-field.full{grid-column:1/-1}
.art-label{font-size:13px;font-weight:700;color:#1b2240}
.art-hint{font-size:12px;color:#6b7a9a;margin-top:2px}
.art-input{padding:11px 14px;border:1px solid #d1e4f6;border-radius:10px;font:14px/1.4 inherit;color:#1b2240;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;width:100%}
.art-input:focus{border-color:#1a56db;box-shadow:0 0 0 3px #dbeafe}
.art-input.is-invalid{border-color:#e04a4a}
select.art-input{appearance:auto;cursor:pointer}
textarea.art-input{resize:vertical}
.art-error{font-size:12px;color:#e04a4a;margin-top:3px}
.art-img-preview{width:100%;max-height:200px;object-fit:cover;border-radius:10px;border:1px solid #dbeafe;margin-bottom:8px}
.art-form-footer{display:flex;align-items:center;gap:12px;padding:22px 32px;background:#f8faff;border-top:1px solid #eff6ff}
.art-submit{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border:0;border-radius:10px;cursor:pointer;color:#fff;background:linear-gradient(100deg,#1a56db,#3b82f6);box-shadow:0 6px 16px #1a56db2a;font:700 14px inherit;transition:opacity .15s,transform .1s}
.art-submit:hover{opacity:.9;transform:translateY(-1px)}
.art-cancel{display:inline-flex;align-items:center;padding:12px 18px;border-radius:10px;border:1px solid #d1e4f6;color:#1e40af;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.art-cancel:hover{background:#f8faff}
.flash-error{display:flex;align-items:center;gap:10px;max-width:960px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fce8e8;color:#962a2a;font-size:14px;font-weight:600}
@media(max-width:700px){.art-grid{grid-template-columns:1fr}}
</style>

<div class="dashboard art-form-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small><a href="{{ route('admin.articles.index') }}" style="color:inherit">Articles</a> / Edit</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if($errors->any())
    <div class="flash-error">✗ Please fix the errors below before saving.</div>
    @endif

    <div class="art-form-hero">
        <div>
            <span class="art-overline">Articles · Edit</span>
            <h1>Edit Article</h1>
            <p>{{ $article->title }}</p>
        </div>
        <div style="font-size:48px">✏️</div>
    </div>

    <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.articles._form')
        <div class="art-form-footer">
            <button type="submit" class="art-submit">Save Changes</button>
            <a href="{{ route('admin.articles.show', $article) }}" class="art-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
(function () {
    const imgInput = document.getElementById('coverImgInput');
    const imgPrev  = document.getElementById('coverImgPreview');
    imgInput?.addEventListener('change', function () {
        const f = this.files[0]; if (!f) return;
        imgPrev.src = URL.createObjectURL(f); imgPrev.style.display = '';
    });
})();
</script>
@endsection
