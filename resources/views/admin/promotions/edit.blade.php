@extends('layouts.app')
@section('title', 'Edit Promotion')
@section('content')
<style>
.prm-form-page{background:radial-gradient(circle at 92% 4%,#ffe8f5 0,transparent 22%),#fdf5fb!important}
.prm-form-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:920px;margin:35px auto 28px;padding:32px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#f783ac 0,transparent 26%),linear-gradient(118deg,#3d0a2e,#8a1f5e)}
.prm-form-hero h1{margin:6px 0 0;font-size:28px;color:#fff}
.prm-form-hero p{margin:5px 0 0;color:#f8cfe6;font-size:14px}
.prm-overline{color:#ffd787;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.prm-form-card{max-width:920px;margin:0 auto;border:1px solid #f0d4e8;border-radius:20px;background:#fff;box-shadow:0 18px 45px #3d0a2e09;overflow:hidden}
.prm-form-section{padding:26px 32px;border-bottom:1px solid #fae8f4}
.prm-form-section:last-child{border-bottom:0}
.prm-section-title{font-size:12px;font-weight:800;color:#7a3060;letter-spacing:.07em;text-transform:uppercase;margin:0 0 16px}
.prm-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.prm-field{display:flex;flex-direction:column;gap:6px}
.prm-field.full{grid-column:1/-1}
.prm-label{font-size:13px;font-weight:700;color:#1b2240}
.prm-hint{font-size:12px;color:#9a7a90;margin-top:2px}
.prm-input{padding:11px 14px;border:1px solid #e8d0e0;border-radius:10px;font:14px/1.4 inherit;color:#1b2240;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;width:100%}
.prm-input:focus{border-color:#8a1f5e;box-shadow:0 0 0 3px #ffe8f5}
.prm-input.is-invalid{border-color:#e04a4a}
select.prm-input{appearance:auto;cursor:pointer}
textarea.prm-input{resize:vertical}
.prm-error{font-size:12px;color:#e04a4a;margin-top:3px}
.prm-img-current{height:100px;border-radius:10px;object-fit:cover;border:1px solid #f0d4e8;display:block;margin-bottom:8px}
.prm-img-preview{width:100%;max-height:160px;object-fit:cover;border-radius:10px;border:1px solid #f0d4e8;margin-top:8px}
.prm-form-footer{display:flex;align-items:center;gap:12px;padding:22px 32px;background:#fdf8fb;border-top:1px solid #fae8f4}
.prm-submit{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border:0;border-radius:10px;cursor:pointer;color:#fff;background:linear-gradient(100deg,#8a1f5e,#c0356e);box-shadow:0 6px 16px #8a1f5e2a;font:700 14px inherit;transition:opacity .15s,transform .1s}
.prm-submit:hover{opacity:.9;transform:translateY(-1px)}
.prm-cancel{display:inline-flex;align-items:center;padding:12px 18px;border-radius:10px;border:1px solid #e8d0e0;color:#7a3060;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.prm-cancel:hover{background:#fdf8fb}
.flash-error{display:flex;align-items:center;gap:10px;max-width:920px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fce8e8;color:#962a2a;font-size:14px;font-weight:600}
@media(max-width:700px){.prm-grid{grid-template-columns:1fr}}
</style>

<div class="dashboard prm-form-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small><a href="{{ route('admin.promotions.index') }}" style="color:inherit">App Content</a> / Edit</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if($errors->any())
    <div class="flash-error">✗ Please fix the errors below before saving.</div>
    @endif

    <div class="prm-form-hero">
        <div>
            <span class="prm-overline">Promotions · Edit</span>
            <h1>{{ $promotion->title }}</h1>
            <p>{{ ucfirst($promotion->type) }} · {{ str_replace('_',' ',ucfirst($promotion->placement)) }} · Status: {{ ucfirst($promotion->status) }}</p>
        </div>
        <div style="font-size:48px">✎</div>
    </div>

    <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="prm-form-card">

            <div class="prm-form-section">
                <p class="prm-section-title">Content</p>
                <div class="prm-grid">
                    <div class="prm-field full">
                        <label class="prm-label">Title (English) *</label>
                        <input type="text" name="title" value="{{ old('title', $promotion->title) }}" class="prm-input @error('title') is-invalid @enderror" required>
                        @error('title')<p class="prm-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="prm-field full">
                        <label class="prm-label">Description (English)</label>
                        <textarea name="description" rows="3" class="prm-input">{{ old('description', $promotion->description) }}</textarea>
                    </div>
                    <div class="prm-field full">
                        <label class="prm-label">Image <span class="prm-hint">(leave blank to keep current)</span></label>
                        @if($promotion->image)
                        <img src="{{ Storage::url($promotion->image) }}" class="prm-img-current" alt="Current image">
                        @endif
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="prm-input" id="imgInput">
                        <img id="imgPreview" src="" class="prm-img-preview" style="display:none">
                        @error('image')<p class="prm-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="prm-form-section">
                <p class="prm-section-title">Hindi Translation <span style="font-weight:500;text-transform:none;letter-spacing:0">(optional)</span></p>
                <div class="prm-grid">
                    <div class="prm-field full">
                        <label class="prm-label">Title (Hindi)</label>
                        <input type="text" name="translations[hi][title]" value="{{ old('translations.hi.title', $promotion->translations['hi']['title'] ?? '') }}" class="prm-input" placeholder="हिन्दी शीर्षक">
                    </div>
                    <div class="prm-field full">
                        <label class="prm-label">Description (Hindi)</label>
                        <textarea name="translations[hi][description]" rows="2" class="prm-input" placeholder="हिन्दी विवरण">{{ old('translations.hi.description', $promotion->translations['hi']['description'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="prm-form-section">
                <p class="prm-section-title">Settings</p>
                <div class="prm-grid">
                    <div class="prm-field">
                        <label class="prm-label">Type *</label>
                        <select name="type" class="prm-input">
                            @foreach($types as $t)
                            <option value="{{ $t }}" @selected(old('type',$promotion->type) === $t)>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="prm-field">
                        <label class="prm-label">Placement *</label>
                        <select name="placement" class="prm-input">
                            @foreach($placements as $pl)
                            <option value="{{ $pl }}" @selected(old('placement',$promotion->placement) === $pl)>{{ str_replace('_',' ',ucfirst($pl)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="prm-field">
                        <label class="prm-label">Status *</label>
                        <select name="status" class="prm-input">
                            @foreach($statuses as $s)
                            <option value="{{ $s }}" @selected(old('status',$promotion->status) === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="prm-field">
                        <label class="prm-label">Audience *</label>
                        <select name="audience" class="prm-input">
                            @foreach($audiences as $a)
                            <option value="{{ $a }}" @selected(old('audience',$promotion->audience) === $a)>{{ ucfirst($a) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="prm-field">
                        <label class="prm-label">CTA type</label>
                        <select name="cta_type" class="prm-input" id="ctaType">
                            @foreach($ctaTypes as $c)
                            <option value="{{ $c }}" @selected(old('cta_type',$promotion->cta_type) === $c)>{{ ucfirst($c) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="prm-field" id="ctaValueField" style="display:none">
                        <label class="prm-label" id="ctaValueLabel">CTA value *</label>

                        {{-- Product (AJAX search) --}}
                        <div id="cta_wrap_product" class="cta-wrap" style="display:none">
                            <select id="cta_cat_filter" class="prm-input" style="margin-bottom:6px">
                                <option value="">— All categories —</option>
                                @foreach($ctaCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->parent_id ? '└ ' : '' }}{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" id="productSearchInput" class="prm-input" placeholder="Type product name or code…" style="margin-bottom:6px" autocomplete="off">
                            <div id="productResults" style="border:1px solid #e8d0e0;border-radius:10px;max-height:220px;overflow-y:auto;display:none;background:#fff"></div>
                            <input type="hidden" name="cta_value" id="cta_sel_product" value="{{ old('cta_type',$promotion->cta_type) === 'product' ? old('cta_value',$promotion->cta_value) : '' }}" disabled>
                            <div id="productSelected" style="display:{{ old('cta_type',$promotion->cta_type) === 'product' && $promotion->cta_value ? '' : 'none' }};margin-top:6px;padding:8px 12px;background:#fdf5fb;border:1px solid #e8d0e0;border-radius:8px;font-size:13px;color:#3d0a2e;font-weight:600">
                                @if(old('cta_type',$promotion->cta_type) === 'product' && $promotion->cta_value)
                                    @php $selProd = \App\Models\Product::find($promotion->cta_value) @endphp
                                    ✓ {{ $selProd?->name ?? 'Product #'.$promotion->cta_value }} [{{ $selProd?->product_code }}]
                                @endif
                            </div>
                        </div>

                        {{-- Category --}}
                        <select name="cta_value" id="cta_sel_category" class="prm-input cta-select cta-wrap" size="6" style="height:auto;display:none" disabled>
                            <option value="">— pick a category —</option>
                            @foreach($ctaCategories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('cta_value',$promotion->cta_value) == $cat->id && old('cta_type',$promotion->cta_type) === 'category')>{{ $cat->parent_id ? '└ ' : '' }}{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        {{-- Pooja --}}
                        <select name="cta_value" id="cta_sel_pooja" class="prm-input cta-select cta-wrap" size="5" style="height:auto;display:none" disabled>
                            <option value="">— pick a service —</option>
                            @foreach($ctaServices as $svc)
                            @php $t=is_array($svc->translations)?$svc->translations:json_decode($svc->translations??'{}',true); $sName=$t['en']['name']??$t['hi']['name']??'Service #'.$svc->id; @endphp
                            <option value="{{ $svc->id }}" @selected(old('cta_value',$promotion->cta_value) == $svc->id && old('cta_type',$promotion->cta_type) === 'pooja')>{{ $sName }}</option>
                            @endforeach
                        </select>

                        {{-- Mataji --}}
                        <select name="cta_value" id="cta_sel_mataji" class="prm-input cta-select cta-wrap" style="display:none" disabled>
                            <option value="">— pick a Mataji —</option>
                            @foreach($ctaMatajis as $m)
                            <option value="{{ $m->id }}" @selected(old('cta_value',$promotion->cta_value) == $m->id && old('cta_type',$promotion->cta_type) === 'mataji')>{{ $m->name }}</option>
                            @endforeach
                        </select>

                        {{-- URL --}}
                        <input type="url" name="cta_value" id="cta_sel_url" class="prm-input cta-select cta-wrap"
                            value="{{ old('cta_type',$promotion->cta_type) === 'url' ? old('cta_value',$promotion->cta_value) : '' }}"
                            placeholder="https://…" style="display:none" disabled>
                    </div>
                    <div class="prm-field">
                        <label class="prm-label">Sort order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $promotion->sort_order) }}" min="0" class="prm-input" style="width:120px">
                    </div>
                </div>
            </div>

            <div class="prm-form-section">
                <p class="prm-section-title">Schedule <span style="font-weight:500;text-transform:none;letter-spacing:0">(leave blank to always show)</span></p>
                <div class="prm-grid">
                    <div class="prm-field">
                        <label class="prm-label">Starts at</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $promotion->starts_at?->format('Y-m-d\TH:i')) }}" class="prm-input">
                    </div>
                    <div class="prm-field">
                        <label class="prm-label">Ends at</label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $promotion->ends_at?->format('Y-m-d\TH:i')) }}" class="prm-input">
                    </div>
                </div>
            </div>

        </div>

        <div class="prm-form-footer">
            <button type="submit" class="prm-submit">Save Changes</button>
            <a href="{{ route('admin.promotions.index') }}" class="prm-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
(function () {
    const CTA_LABELS = { product:'Product', category:'Category', pooja:'Puja / Service', mataji:'Mataji', url:'URL' };
    const CTA_WRAPS  = { product:'cta_wrap_product', category:'cta_sel_category', pooja:'cta_sel_pooja', mataji:'cta_sel_mataji', url:'cta_sel_url' };
    const CTA_SELS   = ['cta_sel_product','cta_sel_category','cta_sel_pooja','cta_sel_mataji','cta_sel_url'];

    const SEARCH_URL = '{{ route("admin.api.products") }}';

    function switchCta(type) {
        document.querySelectorAll('.cta-wrap').forEach(el => el.style.display = 'none');
        CTA_SELS.forEach(id => { const el = document.getElementById(id); if (el) el.disabled = true; });
        const fld = document.getElementById('ctaValueField');
        fld.style.display = type === 'none' ? 'none' : '';
        if (type === 'none') return;
        const wrap = document.getElementById(CTA_WRAPS[type]);
        if (wrap) wrap.style.display = '';
        const sel = document.getElementById('cta_sel_' + type);
        if (sel) sel.disabled = false;
        const lbl = document.getElementById('ctaValueLabel');
        if (lbl) lbl.textContent = (CTA_LABELS[type] || 'CTA value') + ' *';
    }

    // ── Product AJAX search ──────────────────────────────────────────────────
    let searchTimer;
    const searchInput = document.getElementById('productSearchInput');
    const resultsBox  = document.getElementById('productResults');
    const catFilter   = document.getElementById('cta_cat_filter');
    const hiddenInput = document.getElementById('cta_sel_product');
    const selectedBox = document.getElementById('productSelected');

    function doSearch() {
        const q   = (searchInput?.value || '').trim();
        const cat = catFilter?.value || '';
        if (!q && !cat) { if(resultsBox) resultsBox.style.display = 'none'; return; }
        fetch(SEARCH_URL + '?q=' + encodeURIComponent(q) + '&category_id=' + encodeURIComponent(cat),
              { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(products => {
                resultsBox.innerHTML = '';
                if (!products.length) {
                    resultsBox.innerHTML = '<div style="padding:12px 14px;color:#9a7a90;font-size:13px">No products found</div>';
                } else {
                    products.forEach(p => {
                        const d = document.createElement('div');
                        d.style.cssText = 'padding:10px 14px;cursor:pointer;border-bottom:1px solid #f5e8f0;font-size:13px';
                        d.innerHTML = '<span style="color:#9a7a90;font-size:11px;margin-right:6px">[' + p.product_code + ']</span>' + p.name;
                        d.addEventListener('mouseenter', () => d.style.background = '#fdf5fb');
                        d.addEventListener('mouseleave', () => d.style.background = '');
                        d.addEventListener('click', () => {
                            hiddenInput.value = p.id;
                            selectedBox.textContent = '✓ ' + p.name + ' [' + p.product_code + ']';
                            selectedBox.style.display = '';
                            resultsBox.style.display = 'none';
                            searchInput.value = '';
                        });
                        resultsBox.appendChild(d);
                    });
                }
                resultsBox.style.display = '';
            });
    }

    searchInput?.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(doSearch, 300); });
    catFilter?.addEventListener('change', () => { clearTimeout(searchTimer); searchTimer = setTimeout(doSearch, 100); });

    const cta = document.getElementById('ctaType');
    cta.addEventListener('change', () => switchCta(cta.value));
    switchCta(cta.value);

    document.getElementById('imgInput').addEventListener('change', function () {
        const f = this.files[0]; if (!f) return;
        const prev = document.getElementById('imgPreview');
        prev.src = URL.createObjectURL(f); prev.style.display = '';
    });
})();
</script>
@endsection
