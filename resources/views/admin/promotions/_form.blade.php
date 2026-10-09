<div class="store-card" style="max-width:760px">
    <div class="form-grid">

        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Title (English) *</label>
            <input type="text" name="title" value="{{ old('title', $promotion->title ?? '') }}" class="form-input @error('title') is-invalid @enderror" required>
            @error('title')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Description (English)</label>
            <textarea name="description" rows="3" class="form-input">{{ old('description', $promotion->description ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Type *</label>
            <select name="type" class="form-input">
                @foreach($types as $t)
                <option value="{{ $t }}" @selected(old('type', $promotion->type ?? 'banner') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Placement *</label>
            <select name="placement" class="form-input">
                @foreach($placements as $p)
                <option value="{{ $p }}" @selected(old('placement', $promotion->placement ?? 'home_top') === $p)>{{ str_replace('_', ' ', ucfirst($p)) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" class="form-input">
                @foreach($statuses as $s)
                <option value="{{ $s }}" @selected(old('status', $promotion->status ?? 'draft') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Audience *</label>
            <select name="audience" class="form-input">
                @foreach($audiences as $a)
                <option value="{{ $a }}" @selected(old('audience', $promotion->audience ?? 'all') === $a)>{{ ucfirst($a) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">CTA type</label>
            <select name="cta_type" class="form-input" id="cta_type">
                @foreach($ctaTypes as $c)
                <option value="{{ $c }}" @selected(old('cta_type', $promotion->cta_type ?? 'none') === $c)>{{ ucfirst($c) }}</option>
                @endforeach
            </select>
        </div>

        {{-- CTA value — switches based on cta_type selection --}}
        <div class="form-group" id="cta_value_group">
            <label class="form-label" id="cta_value_label">CTA value</label>

            {{-- Product picker --}}
            <div id="cta_val_product_wrap" class="cta-val-field" style="display:none">
                <input type="text" id="product_search" class="form-input" placeholder="Search product…"
                    style="margin-bottom:6px" oninput="filterOptions('cta_val_product', this.value)">
                <select name="cta_value" id="cta_val_product" class="form-input" size="6" style="height:auto">
                    <option value="">— Select product —</option>
                    @foreach($ctaProducts ?? [] as $p)
                        <option value="{{ $p->id }}" @selected(old('cta_value', $promotion->cta_value ?? '') == $p->id && old('cta_type', $promotion->cta_type ?? '') === 'product')>
                            [{{ $p->product_code }}] {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Category picker --}}
            <select name="cta_value" id="cta_val_category" class="form-input cta-val-field" size="6" style="display:none;height:auto">
                <option value="">— Select category —</option>
                @foreach($ctaCategories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('cta_value', $promotion->cta_value ?? '') == $cat->id && old('cta_type', $promotion->cta_type ?? '') === 'category')>
                        {{ $cat->parent_id ? '└ ' : '' }}{{ $cat->name }}
                    </option>
                @endforeach
            </select>

            {{-- Service/Pooja picker --}}
            <select name="cta_value" id="cta_val_pooja" class="form-input cta-val-field" size="5" style="display:none;height:auto">
                <option value="">— Select service —</option>
                @foreach($ctaServices as $svc)
                @php
                    $t = is_array($svc->translations) ? $svc->translations : json_decode($svc->translations ?? '{}', true);
                    $sName = $t['en']['name'] ?? $t['hi']['name'] ?? 'Service #'.$svc->id;
                @endphp
                    <option value="{{ $svc->id }}" @selected(old('cta_value', $promotion->cta_value ?? '') == $svc->id && old('cta_type', $promotion->cta_type ?? '') === 'pooja')>
                        {{ $sName }}
                    </option>
                @endforeach
            </select>

            {{-- Mataji picker --}}
            <select name="cta_value" id="cta_val_mataji" class="form-input cta-val-field" style="display:none;height:auto">
                <option value="">— Select Mataji —</option>
                @foreach($ctaMatajis as $m)
                    <option value="{{ $m->id }}" @selected(old('cta_value', $promotion->cta_value ?? '') == $m->id && old('cta_type', $promotion->cta_type ?? '') === 'mataji')>
                        {{ $m->name }}
                    </option>
                @endforeach
            </select>

            {{-- Donation / Guru picker --}}
            <select name="cta_value" id="cta_val_donation" class="form-input cta-val-field" style="display:none;height:auto">
                <option value="">— Select Guruji —</option>
                @foreach($ctaGurus as $g)
                    <option value="{{ $g->id }}" @selected(old('cta_value', $promotion->cta_value ?? '') == $g->id && old('cta_type', $promotion->cta_type ?? '') === 'donation')>
                        {{ $g->name }}
                    </option>
                @endforeach
            </select>

            {{-- URL input --}}
            <input type="url" name="cta_value" id="cta_val_url" class="form-input cta-val-field" disabled style="display:none"
                value="{{ old('cta_type', $promotion->cta_type ?? '') === 'url' ? old('cta_value', $promotion->cta_value ?? '') : '' }}"
                placeholder="https://…">

        </div>

        <div class="form-group">
            <label class="form-label">Starts at</label>
            <input type="datetime-local" name="starts_at"
                value="{{ old('starts_at', isset($promotion->starts_at) ? $promotion->starts_at->format('Y-m-d\TH:i') : '') }}"
                class="form-input">
        </div>

        <div class="form-group">
            <label class="form-label">Ends at</label>
            <input type="datetime-local" name="ends_at"
                value="{{ old('ends_at', isset($promotion->ends_at) ? $promotion->ends_at->format('Y-m-d\TH:i') : '') }}"
                class="form-input">
        </div>

        <div class="form-group">
            <label class="form-label">Sort order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $promotion->sort_order ?? 0) }}" min="0" class="form-input" style="width:100px">
        </div>

        {{-- Image upload --}}
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Image <small>(jpg/png/webp, max 2 MB)</small></label>
            @if(isset($promotion) && $promotion->image)
            <div style="margin-bottom:8px">
                <img src="{{ Storage::url($promotion->image) }}" style="height:80px;border-radius:4px">
            </div>
            @endif
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-input">
            @error('image')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        {{-- Translations --}}
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label" style="font-weight:600">Hindi translation (optional)</label>
        </div>
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Title (Hindi)</label>
            <input type="text" name="translations[hi][title]"
                value="{{ old('translations.hi.title', $promotion->translations['hi']['title'] ?? '') }}"
                class="form-input" placeholder="हिन्दी शीर्षक">
        </div>
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Description (Hindi)</label>
            <textarea name="translations[hi][description]" rows="2" class="form-input" placeholder="हिन्दी विवरण">{{ old('translations.hi.description', $promotion->translations['hi']['description'] ?? '') }}</textarea>
        </div>

    </div>

    <div style="margin-top:20px;display:flex;gap:10px">
        <button type="submit" class="btn-primary">{{ isset($promotion) ? 'Update' : 'Create' }}</button>
        <a href="{{ route('admin.promotions.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>

<script>
const CTA_LABELS = {
    product:  'Product',
    category: 'Category',
    pooja:    'Puja / Service',
    mataji:   'Mataji',
    donation: 'Guruji (Donation)',
    url:      'URL',
};

// Maps cta_type → the element id to show (wrapper div or input)
const CTA_FIELD_MAP = {
    product:  'cta_val_product_wrap',
    category: 'cta_val_category',
    pooja:    'cta_val_pooja',
    mataji:   'cta_val_mataji',
    donation: 'cta_val_donation',
    url:      'cta_val_url',
};

// All selects/inputs that carry name="cta_value" — disable the hidden ones so they don't submit
const CTA_SUBMIT_IDS = ['cta_val_product', 'cta_val_category', 'cta_val_pooja', 'cta_val_mataji', 'cta_val_donation', 'cta_val_url'];

function switchCta(type) {
    // Hide all wrapper elements
    document.querySelectorAll('.cta-val-field').forEach(el => el.style.display = 'none');
    // Disable all submittable fields
    CTA_SUBMIT_IDS.forEach(id => { const el = document.getElementById(id); if (el) el.disabled = true; });

    document.getElementById('cta_value_group').style.display = type === 'none' ? 'none' : '';
    if (type === 'none') return;

    const wrapperId = CTA_FIELD_MAP[type];
    if (wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (wrapper) wrapper.style.display = '';
    }
    // Enable just the submit field for this type
    const submitEl = document.getElementById('cta_val_' + type);
    if (submitEl) submitEl.disabled = false;

    const lbl = document.getElementById('cta_value_label');
    if (lbl) lbl.textContent = (CTA_LABELS[type] || 'Value') + ' *';
}

function filterOptions(selectId, query) {
    const sel = document.getElementById(selectId);
    if (!sel) return;
    const q = query.toLowerCase();
    Array.from(sel.options).forEach(opt => {
        opt.hidden = q && !opt.text.toLowerCase().includes(q);
    });
}

document.getElementById('cta_type')?.addEventListener('change', function () {
    switchCta(this.value);
});

document.addEventListener('DOMContentLoaded', function () {
    const ct = document.getElementById('cta_type');
    if (ct) switchCta(ct.value);
});
</script>
