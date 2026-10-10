@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.pj-page{background:radial-gradient(circle at 90% 4%,#e6e0ff 0,transparent 22%),#f6f7fc!important}
.pj-wrap{max-width:820px;margin:0 auto;padding-bottom:40px}
.pj-hero{display:flex;align-items:center;gap:24px;margin:35px auto 22px;padding:30px 36px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#9d80ef 0,transparent 26%),linear-gradient(118deg,#1a1442,#3a2d82)}
.pj-hero h1{margin:6px 0 4px;font-size:26px;color:#fff}.pj-hero p{margin:0;color:#d8d4f8;font-size:13px}
.hero-overline{color:#ffdc87;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.pj-section{padding:24px 28px;border:1px solid #e4e7f2;border-radius:16px;background:#fff;margin-bottom:16px;box-shadow:0 4px 14px #1e2a5a06}
.pj-sec-title{font-size:13px;font-weight:800;color:#6b748c;letter-spacing:.06em;text-transform:uppercase;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f0f2f8}
.pj-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#2d3555}
.pj-input,.pj-select,.pj-textarea{width:100%;padding:11px 13px;border:1px solid #e4e7f2;border-radius:10px;font:inherit;color:#1b2240;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.pj-input:focus,.pj-select:focus,.pj-textarea:focus{border-color:#6b5cc8;box-shadow:0 0 0 3px #e6e0ff}
.pj-textarea{resize:vertical;min-height:80px}
.pj-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}
.lang-tabs{display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap}
.lang-tab{padding:7px 14px;border-radius:9px;border:1px solid #e4e7f2;background:#f8f8ff;color:#555e7a;font-size:13px;font-weight:700;cursor:pointer;transition:.15s}
.lang-tab.active,.lang-tab:hover{border-color:#6b5cc8;background:#ede9ff;color:#3a2d82}
.lang-panel{display:none}.lang-panel.active{display:block}
.price-row{display:grid;grid-template-columns:1fr 120px 1fr;gap:12px}
.gallery-grid{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px}
.gallery-item{position:relative;width:90px;height:90px;border-radius:10px;overflow:hidden;border:1px solid #e4e7f2}
.gallery-item img{width:100%;height:100%;object-fit:cover}
.gallery-rm{position:absolute;top:4px;right:4px;display:grid;width:20px;height:20px;place-items:center;border-radius:50%;background:#1b2240bb;color:#fff;cursor:pointer;font-size:10px;border:0}
.img-drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border:2px dashed #d4d0f0;border-radius:12px;padding:18px;background:#f8f8ff;cursor:pointer;transition:.2s;text-align:center}
.img-drop:hover{border-color:#6b5cc8;background:#ede9ff}
.img-preview-sq{width:100px;height:100px;border-radius:12px;overflow:hidden;border:2px solid #d4d0f0;display:none;position:relative}
.img-preview-sq img{width:100%;height:100%;object-fit:cover}
.img-preview-rm{position:absolute;top:4px;right:4px;display:grid;width:20px;height:20px;place-items:center;border-radius:50%;background:#1b2240bb;color:#fff;cursor:pointer;font-size:10px;border:0}
.samagri-row{display:flex;gap:10px;align-items:flex-start;margin-bottom:8px}
.samagri-row .pj-input{flex:1}
.samagri-row .pj-input.price-inp{width:110px;flex:0 0 110px}
.btn-rm-row{display:grid;width:32px;height:38px;place-items:center;border-radius:8px;border:1px solid #fdc9c9;background:#fff0f0;color:#d94040;cursor:pointer;font-size:14px;flex-shrink:0}
.btn-add-row{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;border:1px dashed #6b5cc8;background:#f8f8ff;color:#3a2d82;font:700 13px inherit;cursor:pointer;margin-top:8px;transition:.15s}
.btn-add-row:hover{background:#ede9ff}
.pj-actions{display:flex;gap:12px;justify-content:flex-end;padding:20px 0 0}
.pj-btn-primary{padding:12px 24px;border-radius:11px;border:0;color:#fff;background:linear-gradient(100deg,#3a2d82,#6b5cc8);font:700 14px inherit;cursor:pointer;box-shadow:0 8px 18px #6b5cc825;transition:.2s}
.pj-btn-primary:hover{transform:translateY(-1px)}
.pj-btn-back{padding:12px 16px;border-radius:11px;border:1px solid #e4e7f2;color:#555e7a;background:#fff;font:600 14px inherit;text-decoration:none;display:inline-flex;align-items:center;transition:.15s}
.pj-btn-back:hover{background:#f6f7fc}
.flash-success{display:flex;align-items:center;gap:10px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
</style>
<div class="dashboard pj-page">
    <div class="topbar">
        <div class="page-heading"><span>Poojan › Edit Service</span><small>{{ $service->translate('name') }}</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    <div class="pj-wrap">
        @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

        <div class="pj-hero">
            <div>
                <span class="hero-overline">Edit · Poojan Service</span>
                <h1>{{ $service->translate('name') }}</h1>
                <p>Update name, description, images, samagri list and price.</p>
            </div>
        </div>

        @if($errors->any())
            <div style="padding:13px 16px;border-radius:12px;background:#fff0f0;border:1px solid #fdc9c9;margin-bottom:16px;color:#b33;font-size:13px">
                <strong>Please fix:</strong>
                <ul style="margin:8px 0 0;padding-left:20px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('my.poojan.update', $service) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- ── 1. Name & Description ── --}}
            <div class="pj-section">
                <div class="pj-sec-title">① Name & Description</div>
                <div class="lang-tabs">
                    @foreach($languages as $code => $label)
                        <button type="button" class="lang-tab {{ $code === 'en' ? 'active' : '' }}"
                                onclick="switchLang('{{ $code }}')">{{ $label }}</button>
                    @endforeach
                </div>
                @foreach($languages as $code => $label)
                    @php $t = $service->translations[$code] ?? []; @endphp
                    <div id="lang-{{ $code }}" class="lang-panel {{ $code === 'en' ? 'active' : '' }}">
                        <div style="margin-bottom:14px">
                            <label class="pj-label">Name{{ $code === 'en' ? ' *' : '' }} <span style="font-weight:400;color:#9aa3bc;font-size:12px">({{ $label }})</span></label>
                            <input type="text" name="translations[{{ $code }}][name]" class="pj-input"
                                   value="{{ old("translations.{$code}.name", $t['name'] ?? '') }}"
                                   {{ $code === 'en' ? 'required' : '' }}
                                   placeholder="Service name in {{ $label }}">
                            @error("translations.{$code}.name")<span class="pj-error">{{ $message }}</span>@enderror
                        </div>
                        <div style="margin-bottom:14px">
                            <label class="pj-label">Short Title <span style="font-weight:400;color:#9aa3bc;font-size:12px">({{ $label }})</span></label>
                            <input type="text" name="translations[{{ $code }}][title]" class="pj-input"
                                   value="{{ old("translations.{$code}.title", $t['title'] ?? '') }}"
                                   placeholder="Shown on card in app">
                        </div>
                        <div>
                            <label class="pj-label">Description <span style="font-weight:400;color:#9aa3bc;font-size:12px">({{ $label }})</span></label>
                            <textarea name="translations[{{ $code }}][description]" class="pj-textarea" rows="4"
                                      placeholder="Full description…">{{ old("translations.{$code}.description", $t['description'] ?? '') }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── 2. Price ── --}}
            <div class="pj-section">
                <div class="pj-sec-title">② Price</div>
                <div class="price-row">
                    <div>
                        <label class="pj-label">Amount *</label>
                        <input type="number" name="pricing_amount" class="pj-input" min="0"
                               value="{{ old('pricing_amount', $gs->amount() ?? '') }}" required placeholder="e.g. 5100">
                        @error('pricing_amount')<span class="pj-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="pj-label">Currency *</label>
                        <select name="pricing_currency" class="pj-select">
                            <option value="INR" {{ old('pricing_currency', $gs->currency() ?? 'INR') === 'INR' ? 'selected' : '' }}>INR ₹</option>
                            <option value="USD" {{ old('pricing_currency', $gs->currency() ?? '') === 'USD' ? 'selected' : '' }}>USD $</option>
                        </select>
                    </div>
                    <div>
                        <label class="pj-label">Discount Amount</label>
                        <input type="number" name="pricing_discount_amount" class="pj-input" min="0"
                               value="{{ old('pricing_discount_amount', $gs->discountAmount() ?? '') }}" placeholder="Leave blank if none">
                    </div>
                </div>
            </div>

            {{-- ── 3. Primary Image ── --}}
            <div class="pj-section">
                <div class="pj-sec-title">③ Primary Image</div>
                <div style="display:flex;align-items:flex-start;gap:20px">
                    <div id="primary-preview" class="img-preview-sq" style="{{ $service->primaryImage() ? 'display:block' : '' }}">
                        <img src="{{ $service->primaryImage() ? Storage::disk('public')->url($service->primaryImage()) : '' }}" id="primary-preview-img" alt="">
                        <button type="button" class="img-preview-rm" onclick="removePrimary()">×</button>
                    </div>
                    <div class="img-drop" id="primary-drop" onclick="document.getElementById('primary_image').click()" style="{{ $service->primaryImage() ? 'display:none' : '' }}">
                        <span style="font-size:24px;color:#8b78d8">🖼</span>
                        <p style="margin:0;color:#7a8299;font-size:13px">Click to upload primary image</p>
                        <small style="color:#9aa3bc;font-size:11px">JPG, PNG · max 5 MB</small>
                    </div>
                    <input type="file" id="primary_image" name="primary_image" accept="image/*" style="display:none" onchange="previewPrimary(this)">
                </div>
            </div>

            {{-- ── 4. Gallery Images ── --}}
            <div class="pj-section">
                <div class="pj-sec-title">④ Gallery Images</div>
                <div class="gallery-grid" id="gallery-grid">
                    @foreach($service->gallery() as $path)
                        <div class="gallery-item" id="gi-{{ md5($path) }}">
                            <img src="{{ Storage::disk('public')->url($path) }}" alt="">
                            <button type="button" class="gallery-rm" onclick="removeGallery('{{ $path }}', '{{ md5($path) }}')">×</button>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:12px">
                    <label class="pj-label" style="margin-bottom:8px">Add more images</label>
                    <input type="file" name="gallery_images[]" accept="image/*" multiple>
                </div>
                <div id="remove-gallery-inputs"></div>
            </div>

            {{-- ── 5. Samagri List ── --}}
            <div class="pj-section">
                <div class="pj-sec-title">⑤ Samagri List</div>
                <div id="samagri-list">
                    @php $samagri = $gs->samagri() ?? []; $si = 0; @endphp
                    @foreach($samagri as $item)
                        <div class="samagri-row" id="sr-{{ $si }}">
                            <input type="text" name="pooja_samagri[{{ $si }}][name]" class="pj-input"
                                   value="{{ $item['name'] ?? '' }}" placeholder="Item name (e.g. Camphor)" required>
                            <input type="number" name="pooja_samagri[{{ $si }}][price]" class="pj-input price-inp"
                                   value="{{ $item['price'] ?? '' }}" placeholder="Price" step="0.01" min="0">
                            <button type="button" class="btn-rm-row" onclick="removeSamagri({{ $si }})">✕</button>
                        </div>
                        @php $si++; @endphp
                    @endforeach
                </div>
                <button type="button" class="btn-add-row" onclick="addSamagri()">＋ Add Item</button>
            </div>

            <div class="pj-actions">
                <a href="{{ route('my.poojan.index') }}" class="pj-btn-back">← Back</a>
                <button type="submit" class="pj-btn-primary">💾 Save Changes</button>
            </div>
        </form>
    </div>
</div>
<script>
let si = {{ count($samagri ?? []) }};

function switchLang(code) {
    document.querySelectorAll('.lang-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.lang-panel').forEach(p => p.classList.remove('active'));
    document.querySelector('.lang-tab[onclick*="' + code + '"]').classList.add('active');
    document.getElementById('lang-' + code).classList.add('active');
}

function previewPrimary(input) {
    if (!input.files[0]) return;
    document.getElementById('primary-preview-img').src = URL.createObjectURL(input.files[0]);
    document.getElementById('primary-preview').style.display = 'block';
    document.getElementById('primary-drop').style.display = 'none';
}
function removePrimary() {
    document.getElementById('primary-preview').style.display = 'none';
    document.getElementById('primary-drop').style.display = 'flex';
    document.getElementById('primary_image').value = '';
}

function removeGallery(path, hash) {
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'remove_gallery[]'; inp.value = path;
    document.getElementById('remove-gallery-inputs').appendChild(inp);
    const el = document.getElementById('gi-' + hash);
    if (el) el.remove();
}

function addSamagri() {
    const div = document.createElement('div');
    div.className = 'samagri-row'; div.id = 'sr-' + si;
    div.innerHTML = `<input type="text" name="pooja_samagri[${si}][name]" class="pj-input" placeholder="Item name (e.g. Camphor)" required>
        <input type="number" name="pooja_samagri[${si}][price]" class="pj-input price-inp" placeholder="Price" step="0.01" min="0">
        <button type="button" class="btn-rm-row" onclick="removeSamagri(${si})">✕</button>`;
    document.getElementById('samagri-list').appendChild(div);
    si++;
}

function removeSamagri(idx) {
    const el = document.getElementById('sr-' + idx);
    if (el) el.remove();
}

if (si === 0) addSamagri();
</script>
@endsection
