@extends('layouts.app')
@section('content')
<style>
.svc-me-page{background:radial-gradient(circle at 90% 4%,#e6e0ff 0,transparent 22%),#f6f7fc!important}
.svc-me-wrap{max-width:800px;margin:0 auto;padding-bottom:40px}
.svc-me-hero{display:flex;align-items:center;gap:24px;margin:35px auto 22px;padding:30px 36px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#9d80ef 0,transparent 26%),linear-gradient(118deg,#1a1442,#3a2d82)}
.svc-me-hero h1{margin:6px 0 4px;font-size:26px;color:#fff}
.svc-me-hero p{margin:0;color:#d8d4f8;font-size:13px}
.hero-overline{color:#ffdc87;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.me-section{padding:24px 28px;border:1px solid #e4e7f2;border-radius:16px;background:#fff;margin-bottom:16px;box-shadow:0 4px 14px #1e2a5a06}
.me-title{font-size:13px;font-weight:800;color:#6b748c;letter-spacing:.06em;text-transform:uppercase;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f0f2f8}
.me-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#2d3555}
.me-input,.me-textarea{width:100%;padding:11px 13px;border:1px solid #e4e7f2;border-radius:10px;font:inherit;color:#1b2240;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.me-input:focus,.me-textarea:focus{border-color:#6b5cc8;box-shadow:0 0 0 3px #e6e0ff}
.me-textarea{resize:vertical;min-height:80px}
.me-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}
.lang-tabs{display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap}
.lang-tab{padding:7px 14px;border-radius:9px;border:1px solid #e4e7f2;background:#f8f8ff;color:#555e7a;font-size:13px;font-weight:700;cursor:pointer;transition:.15s}
.lang-tab.active,.lang-tab:hover{border-color:#6b5cc8;background:#ede9ff;color:#3a2d82}
.lang-panel{display:none}.lang-panel.active{display:block}
.me-actions{display:flex;gap:12px;justify-content:flex-end;padding:20px 0 0}
.me-btn-primary{padding:12px 24px;border-radius:11px;border:0;color:#fff;background:linear-gradient(100deg,#3a2d82,#6b5cc8);font:700 14px inherit;cursor:pointer;box-shadow:0 8px 18px #6b5cc825;transition:.2s}
.me-btn-primary:hover{transform:translateY(-1px)}
.me-btn-back{padding:12px 16px;border-radius:11px;border:1px solid #e4e7f2;color:#555e7a;background:#fff;font:600 14px inherit;text-decoration:none;display:inline-flex;align-items:center;transition:.15s}
.me-btn-back:hover{background:#f6f7fc}
.flash-success{display:flex;align-items:center;gap:10px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
</style>
<div class="dashboard svc-me-page">
    <div class="topbar">
        <div class="page-heading"><span>Services › Edit My Content</span><small>Update name and description for your assigned service</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="svc-me-wrap">
        @if(session('success'))
            <div class="flash-success">✓ {{ session('success') }}</div>
        @endif

        <div class="svc-me-hero">
            <div>
                <span class="hero-overline">My Services</span>
                <h1>{{ $service->translate('name') }}</h1>
                <p>Edit the name, title and description shown to users in the app.</p>
            </div>
        </div>

        @if($errors->any())
            <div style="padding:13px 16px;border-radius:12px;background:#fff0f0;border:1px solid #fdc9c9;margin-bottom:16px;color:#b33;font-size:13px">
                <strong>Please fix:</strong>
                <ul style="margin:8px 0 0;padding-left:20px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('guru-services.update-content', $service) }}">
            @csrf @method('PUT')

            <div class="me-section">
                <div class="me-title">Content — per language</div>

                <div class="lang-tabs">
                    @foreach($languages as $code => $label)
                        <button type="button" class="lang-tab {{ $code === 'en' ? 'active' : '' }}"
                                onclick="switchLang('{{ $code }}')">{{ $label }}</button>
                    @endforeach
                </div>

                @foreach($languages as $code => $label)
                    @php
                        $t = $service->translations[$code] ?? [];
                    @endphp
                    <div id="lang-{{ $code }}" class="lang-panel {{ $code === 'en' ? 'active' : '' }}">
                        <div style="margin-bottom:14px">
                            <label class="me-label">
                                Name{{ $code === 'en' ? ' *' : '' }}
                                <span style="font-weight:400;color:#9aa3bc;font-size:12px">({{ $label }})</span>
                            </label>
                            <input type="text" name="translations[{{ $code }}][name]"
                                   class="me-input"
                                   value="{{ old("translations.{$code}.name", $t['name'] ?? '') }}"
                                   {{ $code === 'en' ? 'required' : '' }}
                                   placeholder="Service name in {{ $label }}">
                            @error("translations.{$code}.name")<span class="me-error">{{ $message }}</span>@enderror
                        </div>
                        <div style="margin-bottom:14px">
                            <label class="me-label">Title / Sub-heading <span style="font-weight:400;color:#9aa3bc;font-size:12px">({{ $label }})</span></label>
                            <input type="text" name="translations[{{ $code }}][title]"
                                   class="me-input"
                                   value="{{ old("translations.{$code}.title", $t['title'] ?? '') }}"
                                   placeholder="Short title shown on card">
                        </div>
                        <div>
                            <label class="me-label">Description <span style="font-weight:400;color:#9aa3bc;font-size:12px">({{ $label }})</span></label>
                            <textarea name="translations[{{ $code }}][description]"
                                      class="me-textarea" rows="4"
                                      placeholder="Full description of the service…">{{ old("translations.{$code}.description", $t['description'] ?? '') }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="me-actions">
                <a href="{{ route('services.show', $service) }}" class="me-btn-back">← Back</a>
                <button type="submit" class="me-btn-primary">💾 Save Changes</button>
            </div>
        </form>
    </div>
</div>
<script>
function switchLang(code) {
    document.querySelectorAll('.lang-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.lang-panel').forEach(p => p.classList.remove('active'));
    document.querySelector('.lang-tab[onclick*="' + code + '"]').classList.add('active');
    document.getElementById('lang-' + code).classList.add('active');
}
</script>
@endsection
