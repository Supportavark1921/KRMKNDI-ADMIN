@use('Illuminate\Support\Facades\Storage')
{{-- Reusable service form partial
     Props: $service (optional, for edit), $languages (all supported), $action, $method --}}

@php
    $isEdit       = isset($service);
    $old          = fn($key, $fallback = '') => old($key, $isEdit ? data_get($service, $key, $fallback) : $fallback);
    $langs        = $languages;                     // all supported languages
    $activeLangs  = $isEdit ? array_unique(array_merge(['en'], $service->activeLanguages())) : ['en'];
    $inactiveLangs = array_diff(array_keys($langs), $activeLangs);
@endphp

<style>
.sf-wrap{max-width:960px;margin:0 auto}
.sf-section{padding:28px 32px;border:1px solid #e4e7f2;border-radius:18px;background:#fff;margin-bottom:18px;box-shadow:0 4px 14px #1e2a5a06}
.sf-section-title{display:flex;align-items:center;gap:10px;margin:0 0 22px;font-size:16px;font-weight:800;color:#1b2240}
.sf-section-title span{display:grid;width:30px;height:30px;place-items:center;border-radius:9px;background:#ede9ff;color:#6246ea;font-size:14px}
.sf-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#374060}
.sf-input,.sf-select,.sf-textarea{width:100%;padding:11px 13px;border:1px solid #dde1ee;border-radius:10px;font:inherit;color:#1b2240;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.sf-input:focus,.sf-select:focus,.sf-textarea:focus{border-color:#6246ea;box-shadow:0 0 0 3px #ede9ff}
.sf-textarea{resize:vertical;min-height:100px}
.sf-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.sf-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
.sf-help{display:block;margin-top:5px;color:#8b96b0;font-size:11px}
.sf-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}

/* Language tabs */
.lang-tabs{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:24px}
.lang-tab{display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid #e4e7f2;background:#f8f8ff;color:#555e7a;font-size:13px;font-weight:700;cursor:pointer;transition:.15s;user-select:none}
.lang-tab.active{border-color:#6246ea;background:#ede9ff;color:#4934c4}
.lang-tab .lang-remove{display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#c4b7f5;color:#3a29a0;font-size:10px;margin-left:2px;cursor:pointer;transition:.15s}
.lang-tab .lang-remove:hover{background:#e04a4a;color:#fff}
.lang-add-btn{display:flex;align-items:center;gap:5px;padding:8px 12px;border-radius:10px;border:1px dashed #c4b7f5;background:transparent;color:#6246ea;font-size:13px;font-weight:700;cursor:pointer;transition:.15s}
.lang-add-btn:hover{background:#ede9ff;border-color:#6246ea}
.lang-panel{display:none}.lang-panel.active{display:block}
.lang-dropdown{position:relative}
.lang-dropdown-menu{display:none;position:absolute;top:calc(100% + 6px);left:0;z-index:50;min-width:180px;padding:6px;border:1px solid #e4e7f2;border-radius:12px;background:#fff;box-shadow:0 8px 24px #1e2a5a14}
.lang-dropdown-menu.open{display:block}
.lang-dropdown-item{padding:9px 12px;border-radius:8px;font-size:13px;font-weight:600;color:#374060;cursor:pointer;transition:.15s}
.lang-dropdown-item:hover{background:#ede9ff;color:#4934c4}
.lang-dropdown-item.added{color:#b0b8c8;cursor:default}
.lang-dropdown-item.added:hover{background:transparent}

/* Image upload */
.img-primary-wrap{display:grid;grid-template-columns:180px 1fr;gap:20px;align-items:start}
.img-drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;border:2px dashed #c4b7f5;border-radius:14px;padding:28px 20px;background:#f9f8ff;cursor:pointer;transition:.2s;text-align:center}
.img-drop:hover,.img-drop.drag-over{border-color:#6246ea;background:#ede9ff}
.img-drop-icon{font-size:28px;color:#8b78d8}
.img-drop p{margin:0;color:#7882a0;font-size:13px}
.img-drop small{color:#a0a9bf;font-size:11px}
.img-preview{position:relative;width:180px;height:160px;border-radius:14px;overflow:hidden;border:1px solid #e4e7f2;display:none}
.img-preview img{width:100%;height:100%;object-fit:cover}
.img-preview-remove{position:absolute;top:8px;right:8px;display:grid;width:26px;height:26px;place-items:center;border-radius:50%;background:#1b2240cc;color:#fff;cursor:pointer;font-size:14px;border:0}
.img-gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px;margin-top:14px}
.gallery-item{position:relative;border-radius:12px;overflow:hidden;border:1px solid #e4e7f2;aspect-ratio:4/3}
.gallery-item img{width:100%;height:100%;object-fit:cover}
.gallery-item-remove{position:absolute;top:6px;right:6px;display:grid;width:24px;height:24px;place-items:center;border-radius:50%;background:#1b2240bb;color:#fff;cursor:pointer;font-size:12px;border:0}
.gallery-drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border:2px dashed #c4b7f5;border-radius:14px;padding:24px;background:#f9f8ff;cursor:pointer;transition:.2s;grid-column:1/-1}
.gallery-drop:hover,.gallery-drop.drag-over{border-color:#6246ea;background:#ede9ff}

/* Toggle */
.sf-toggle-wrap{display:flex;align-items:center;gap:14px}
.sf-toggle{position:relative;width:46px;height:26px;flex-shrink:0}
.sf-toggle input{position:absolute;opacity:0;width:0;height:0}
.sf-toggle-track{position:absolute;inset:0;border-radius:13px;background:#d0d6e8;transition:.2s;cursor:pointer}
.sf-toggle-track::after{position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:50%;background:#fff;content:"";transition:.2s;box-shadow:0 1px 4px #0002}
.sf-toggle input:checked+.sf-toggle-track{background:#6246ea}
.sf-toggle input:checked+.sf-toggle-track::after{transform:translateX(20px)}
.sf-toggle-label{font-size:14px;font-weight:700;color:#374060;cursor:pointer}

/* Form actions */
.sf-actions{display:flex;align-items:center;gap:12px;justify-content:flex-end;padding:20px 0 0}
.sf-btn-primary{padding:13px 28px;border-radius:11px;border:0;color:#fff;background:linear-gradient(100deg,#5c4bb7,#7666d4);font:700 15px inherit;cursor:pointer;box-shadow:0 8px 18px #5c4bb725;transition:.2s}
.sf-btn-primary:hover{transform:translateY(-1px);box-shadow:0 12px 22px #5c4bb735}
.sf-btn-secondary{padding:13px 20px;border-radius:11px;border:1px solid #e4e7f2;color:#555e7a;background:#fff;font:600 14px inherit;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;transition:.15s}
.sf-btn-secondary:hover{background:#f4f5fb;color:#1b2240}

/* Required star */
.req{color:#d94040;margin-left:3px}

/* Price prefix */
.sf-input-prefix{position:relative}
.sf-input-prefix span{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#8b96b0;font-weight:700;pointer-events:none}
.sf-input-prefix input{padding-left:28px}

@media(max-width:700px){.sf-row,.sf-row-3,.img-primary-wrap{grid-template-columns:1fr}.sf-wrap{padding:0}}
</style>

<div class="sf-wrap">
    @if($errors->any())
        <div style="padding:14px 16px;border-radius:12px;background:#fff0f0;border:1px solid #fdc9c9;margin-bottom:18px;color:#b33;font-size:13px">
            <strong>Please fix the following:</strong>
            <ul style="margin:8px 0 0;padding-left:20px">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="service-form">
        @csrf
        @if(isset($method))<input type="hidden" name="_method" value="{{ $method }}">@endif

        {{-- ── 1. Translations ──────────────────────────────────────────── --}}
        <div class="sf-section">
            <h2 class="sf-section-title"><span>🌐</span> Content &amp; Translations</h2>

            {{-- Language tab bar --}}
            <div class="lang-tabs" id="lang-tabs">
                @foreach($activeLangs as $lang)
                    <div class="lang-tab {{ $loop->first ? 'active' : '' }}" data-lang="{{ $lang }}" onclick="switchLang('{{ $lang }}')">
                        {{ $langs[$lang] ?? strtoupper($lang) }}
                        @if($lang !== 'en')
                            <span class="lang-remove" onclick="removeLang(event,'{{ $lang }}')" title="Remove language">×</span>
                        @endif
                    </div>
                @endforeach
                <div class="lang-dropdown" id="lang-dropdown">
                    <button type="button" class="lang-add-btn" onclick="toggleLangMenu()" id="lang-add-btn">＋ Add Language</button>
                    <div class="lang-dropdown-menu" id="lang-menu">
                        @foreach($langs as $code => $label)
                            <div class="lang-dropdown-item {{ in_array($code, $activeLangs) ? 'added' : '' }}"
                                 data-code="{{ $code }}"
                                 data-label="{{ $label }}"
                                 onclick="addLang('{{ $code }}','{{ $label }}')">
                                {{ $label }} <span style="color:#b0b8c8;font-size:11px;margin-left:6px">({{ strtoupper($code) }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Per-language panels --}}
            @foreach($langs as $langCode => $langLabel)
                <div class="lang-panel {{ in_array($langCode, $activeLangs) ? '' : 'hidden-lang' }}"
                     id="panel-{{ $langCode }}"
                     style="{{ in_array($langCode, $activeLangs) ? ($langCode === 'en' ? '' : 'display:none') : 'display:none' }}">

                    <div style="margin-bottom:16px">
                        <label class="sf-label">Name <span class="req">{{ $langCode === 'en' ? '*' : '' }}</span></label>
                        <input type="text"
                               name="translations[{{ $langCode }}][name]"
                               class="sf-input"
                               value="{{ old('translations.'.$langCode.'.name', $isEdit ? ($service->translations[$langCode]['name'] ?? '') : '') }}"
                               placeholder="{{ $langCode === 'en' ? 'e.g. Hair Spa' : 'e.g. '.$langLabel.' translation' }}"
                               {{ $langCode === 'en' ? 'required' : '' }}>
                        @error('translations.'.$langCode.'.name')<span class="sf-error">{{ $message }}</span>@enderror
                    </div>

                    <div style="margin-bottom:16px">
                        <label class="sf-label">Title / Tagline</label>
                        <input type="text"
                               name="translations[{{ $langCode }}][title]"
                               class="sf-input"
                               value="{{ old('translations.'.$langCode.'.title', $isEdit ? ($service->translations[$langCode]['title'] ?? '') : '') }}"
                               placeholder="Short tagline shown to customers">
                        @error('translations.'.$langCode.'.title')<span class="sf-error">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="sf-label">Description</label>
                        <textarea name="translations[{{ $langCode }}][description]"
                                  class="sf-textarea"
                                  rows="4"
                                  placeholder="Full description of this service…">{{ old('translations.'.$langCode.'.description', $isEdit ? ($service->translations[$langCode]['description'] ?? '') : '') }}</textarea>
                        @error('translations.'.$langCode.'.description')<span class="sf-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── 2. Pricing ───────────────────────────────────────────────── --}}
        <div class="sf-section">
            <h2 class="sf-section-title"><span>₹</span> Pricing</h2>
            <div class="sf-row-3">
                <div>
                    <label class="sf-label">Price <span class="req">*</span></label>
                    <div class="sf-input-prefix">
                        <span>₹</span>
                        <input type="number" name="pricing[amount]" class="sf-input"
                               value="{{ old('pricing.amount', $isEdit ? $service->amount() : '') }}"
                               min="0" max="9999999" step="1" required placeholder="0">
                    </div>
                    @error('pricing.amount')<span class="sf-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="sf-label">Currency</label>
                    <select name="pricing[currency]" class="sf-select">
                        @foreach(['INR' => 'INR (₹)', 'USD' => 'USD ($)', 'EUR' => 'EUR (€)'] as $code => $label)
                            <option value="{{ $code }}" @selected(old('pricing.currency', $isEdit ? $service->currency() : 'INR') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="sf-label">Discount Price</label>
                    <div class="sf-input-prefix">
                        <span>₹</span>
                        <input type="number" name="pricing[discount_amount]" class="sf-input"
                               value="{{ old('pricing.discount_amount', $isEdit ? $service->discountAmount() : '') }}"
                               min="0" max="9999999" step="1" placeholder="Optional">
                    </div>
                    <span class="sf-help">Leave blank if no discount</span>
                </div>
            </div>
        </div>

        {{-- ── 3. Images ────────────────────────────────────────────────── --}}
        <div class="sf-section">
            <h2 class="sf-section-title"><span>🖼</span> Images</h2>

            {{-- Primary image --}}
            <div style="margin-bottom:24px">
                <label class="sf-label">Primary Image</label>
                <div class="img-primary-wrap">
                    {{-- Preview --}}
                    <div id="primary-preview" class="img-preview" style="{{ $isEdit && $service->primaryImage() ? 'display:block' : 'display:none' }}">
                        @if($isEdit && $service->primaryImage())
                            <img src="{{ Storage::disk('public')->url($service->primaryImage()) }}" id="primary-preview-img" alt="">
                        @else
                            <img src="" id="primary-preview-img" alt="">
                        @endif
                        <button type="button" class="img-preview-remove" onclick="removePrimary()" title="Remove">×</button>
                    </div>
                    {{-- Drop zone --}}
                    <div class="img-drop" id="primary-drop" onclick="document.getElementById('primary-file').click()"
                         style="{{ $isEdit && $service->primaryImage() ? 'display:none' : '' }}">
                        <span class="img-drop-icon">📷</span>
                        <p>Click or drag image here</p>
                        <small>JPG, PNG, WebP · max 5 MB</small>
                    </div>
                    <input type="file" id="primary-file" name="primary_image" accept="image/*" style="display:none" onchange="previewPrimary(this)">
                </div>
            </div>

            {{-- Gallery --}}
            <div>
                <label class="sf-label">Gallery Images</label>
                <input type="hidden" name="remove_gallery" id="remove-gallery-input" value="[]">
                <input type="hidden" name="gallery_order" id="gallery-order-input">
                <div class="img-gallery-grid" id="gallery-grid">
                    @if($isEdit)
                        @foreach($service->gallery() as $path)
                            <div class="gallery-item" data-path="{{ $path }}">
                                <img src="{{ Storage::disk('public')->url($path) }}" alt="">
                                <button type="button" class="gallery-item-remove" onclick="removeGalleryItem(this,'{{ $path }}')" title="Remove">×</button>
                            </div>
                        @endforeach
                    @endif
                    <div class="gallery-drop" id="gallery-drop" onclick="document.getElementById('gallery-file').click()">
                        <span style="font-size:24px;color:#8b78d8">＋</span>
                        <p style="margin:0;color:#7882a0;font-size:13px">Add gallery images</p>
                        <small style="color:#a0a9bf;font-size:11px">Multiple files allowed · JPG, PNG, WebP</small>
                    </div>
                </div>
                <input type="file" id="gallery-file" name="gallery_images[]" accept="image/*" multiple style="display:none" onchange="addGalleryFiles(this)">
            </div>
        </div>

        {{-- ── 4. Pooja Samagri ─────────────────────────────────────────── --}}
        <div class="sf-section">
            <h2 class="sf-section-title"><span>🪔</span> Pooja Samagri</h2>
            <p style="margin:0 0 18px;color:#8b96b0;font-size:13px">List all items needed for this poojan. These are shown to the customer before booking.</p>

            <div id="samagri-list" style="display:grid;gap:10px;margin-bottom:14px">
                @php $existingSamagri = $isEdit ? ($service->pooja_samagri ?? []) : []; @endphp
                @forelse($existingSamagri as $i => $item)
                <div class="samagri-row" style="display:grid;grid-template-columns:1fr 140px 36px;gap:10px;align-items:center">
                    <input type="text" name="pooja_samagri[{{ $i }}][name]" class="sf-input"
                           placeholder="Item name (e.g. Ghee, Flowers, Camphor)"
                           value="{{ old('pooja_samagri.'.$i.'.name', $item['name'] ?? '') }}" required>
                    <div class="sf-input-prefix">
                        <span>₹</span>
                        <input type="number" name="pooja_samagri[{{ $i }}][price]" class="sf-input"
                               placeholder="Price" min="0" step="0.01"
                               value="{{ old('pooja_samagri.'.$i.'.price', $item['price'] ?? '') }}">
                    </div>
                    <button type="button" onclick="removeSamagriRow(this)"
                            style="width:36px;height:40px;border:1px solid #fdc9c9;border-radius:10px;background:#fff0f0;color:#d94040;font-size:18px;cursor:pointer;line-height:1">×</button>
                </div>
                @empty
                {{-- empty: first row added by JS on page load --}}
                @endforelse
            </div>

            <button type="button" onclick="addSamagriRow()"
                    style="display:inline-flex;align-items:center;gap:7px;padding:10px 16px;border:1px dashed #c4b7f5;border-radius:10px;background:transparent;color:#6246ea;font:700 13px inherit;cursor:pointer;transition:.15s"
                    onmouseover="this.style.background='#ede9ff'" onmouseout="this.style.background='transparent'">
                ＋ Add Samagri Item
            </button>
            <span class="sf-help" style="margin-left:12px">Leave price blank if not applicable.</span>
        </div>

        {{-- ── 5. Settings ──────────────────────────────────────────────── --}}
        <div class="sf-section">
            <h2 class="sf-section-title"><span>⚙</span> Settings</h2>
            <div class="sf-toggle-wrap">
                <label class="sf-toggle">
                    <input type="checkbox" name="status_toggle" id="status-toggle"
                           {{ old('status', $isEdit ? $service->status : 'active') === 'active' ? 'checked' : '' }}
                           onchange="document.getElementById('status-field').value=this.checked?'active':'inactive'">
                    <span class="sf-toggle-track"></span>
                </label>
                <input type="hidden" name="status" id="status-field"
                       value="{{ old('status', $isEdit ? $service->status : 'active') }}">
                <label for="status-toggle" class="sf-toggle-label">Service is Active</label>
            </div>
            <span class="sf-help">Inactive services are hidden from the mobile app.</span>
        </div>

        {{-- ── Actions ──────────────────────────────────────────────────── --}}
        <div class="sf-actions">
            <a href="{{ route('services.index') }}" class="sf-btn-secondary">Cancel</a>
            <button type="submit" class="sf-btn-primary">{{ $isEdit ? '💾 Save Changes' : '✦ Create Service' }}</button>
        </div>
    </form>
</div>

<script>
// ── Language management ─────────────────────────────────────────────────────
let activeLangs = @json($activeLangs);
let currentLang = 'en';

function switchLang(lang) {
    currentLang = lang;
    document.querySelectorAll('.lang-tab').forEach(t => t.classList.toggle('active', t.dataset.lang === lang));
    document.querySelectorAll('.lang-panel').forEach(p => p.style.display = 'none');
    const panel = document.getElementById('panel-' + lang);
    if (panel) panel.style.display = 'block';
}

function addLang(code, label) {
    if (activeLangs.includes(code)) return;
    activeLangs.push(code);

    // Add tab
    const tab = document.createElement('div');
    tab.className = 'lang-tab';
    tab.dataset.lang = code;
    tab.setAttribute('onclick', `switchLang('${code}')`);
    tab.innerHTML = `${label} <span class="lang-remove" onclick="removeLang(event,'${code}')" title="Remove language">×</span>`;
    document.getElementById('lang-tabs').insertBefore(tab, document.getElementById('lang-dropdown'));

    // Show panel
    const panel = document.getElementById('panel-' + code);
    if (panel) { panel.style.display = 'none'; panel.classList.remove('hidden-lang'); }

    // Mark menu item as added
    document.querySelectorAll('.lang-dropdown-item').forEach(item => {
        if (item.dataset.code === code) item.classList.add('added');
    });

    closeLangMenu();
    switchLang(code);
}

function removeLang(e, code) {
    e.stopPropagation();
    if (!confirm('Remove ' + code.toUpperCase() + ' translation? Entered content will be lost.')) return;
    activeLangs = activeLangs.filter(l => l !== code);

    // Remove tab
    document.querySelectorAll('.lang-tab').forEach(t => { if (t.dataset.lang === code) t.remove(); });

    // Hide + clear panel
    const panel = document.getElementById('panel-' + code);
    if (panel) {
        panel.style.display = 'none';
        panel.querySelectorAll('input,textarea').forEach(el => el.value = '');
    }

    // Unmark menu item
    document.querySelectorAll('.lang-dropdown-item').forEach(item => {
        if (item.dataset.code === code) item.classList.remove('added');
    });

    if (currentLang === code) switchLang('en');
}

function toggleLangMenu() { document.getElementById('lang-menu').classList.toggle('open'); }
function closeLangMenu()   { document.getElementById('lang-menu').classList.remove('open'); }
document.addEventListener('click', e => { if (!document.getElementById('lang-dropdown').contains(e.target)) closeLangMenu(); });

// ── Primary image ────────────────────────────────────────────────────────────
function previewPrimary(input) {
    if (!input.files[0]) return;
    const url = URL.createObjectURL(input.files[0]);
    document.getElementById('primary-preview-img').src = url;
    document.getElementById('primary-preview').style.display = 'block';
    document.getElementById('primary-drop').style.display = 'none';
}
function removePrimary() {
    document.getElementById('primary-preview').style.display = 'none';
    document.getElementById('primary-drop').style.display = 'flex';
    document.getElementById('primary-file').value = '';
    const img = document.getElementById('primary-preview-img');
    img.src = '';
}

// Primary drag-drop
const primaryDrop = document.getElementById('primary-drop');
primaryDrop.addEventListener('dragover', e => { e.preventDefault(); primaryDrop.classList.add('drag-over'); });
primaryDrop.addEventListener('dragleave', () => primaryDrop.classList.remove('drag-over'));
primaryDrop.addEventListener('drop', e => {
    e.preventDefault(); primaryDrop.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer(); dt.items.add(file);
        document.getElementById('primary-file').files = dt.files;
        previewPrimary(document.getElementById('primary-file'));
    }
});

// ── Gallery images ────────────────────────────────────────────────────────────
let removedGallery = [];

function addGalleryFiles(input) {
    const grid = document.getElementById('gallery-grid');
    const drop = document.getElementById('gallery-drop');
    Array.from(input.files).forEach(file => {
        const url = URL.createObjectURL(file);
        const item = document.createElement('div');
        item.className = 'gallery-item new-upload';
        item.innerHTML = `<img src="${url}" alt=""><button type="button" class="gallery-item-remove" onclick="removeNewGallery(this)" title="Remove">×</button>`;
        grid.insertBefore(item, drop);
    });
}

function removeGalleryItem(btn, path) {
    removedGallery.push(path);
    document.getElementById('remove-gallery-input').value = JSON.stringify(removedGallery);
    btn.closest('.gallery-item').remove();
}

function removeNewGallery(btn) {
    btn.closest('.gallery-item').remove();
}

// ── Pooja Samagri ────────────────────────────────────────────────────────────
let samagriIdx = {{ count($existingSamagri ?? []) }};

function addSamagriRow() {
    const i = samagriIdx++;
    const row = document.createElement('div');
    row.className = 'samagri-row';
    row.style = 'display:grid;grid-template-columns:1fr 140px 36px;gap:10px;align-items:center';
    row.innerHTML = `
        <input type="text" name="pooja_samagri[${i}][name]" class="sf-input"
               placeholder="Item name (e.g. Ghee, Flowers, Camphor)" required>
        <div class="sf-input-prefix">
            <span>₹</span>
            <input type="number" name="pooja_samagri[${i}][price]" class="sf-input"
                   placeholder="Price" min="0" step="0.01">
        </div>
        <button type="button" onclick="removeSamagriRow(this)"
                style="width:36px;height:40px;border:1px solid #fdc9c9;border-radius:10px;background:#fff0f0;color:#d94040;font-size:18px;cursor:pointer;line-height:1">×</button>`;
    document.getElementById('samagri-list').appendChild(row);
    row.querySelector('input[type=text]').focus();
}

function removeSamagriRow(btn) {
    btn.closest('.samagri-row').remove();
}

// Add one blank row on create if none exist
if (samagriIdx === 0) addSamagriRow();

// Gallery drag-drop
const galleryDrop = document.getElementById('gallery-drop');
galleryDrop.addEventListener('dragover', e => { e.preventDefault(); galleryDrop.classList.add('drag-over'); });
galleryDrop.addEventListener('dragleave', () => galleryDrop.classList.remove('drag-over'));
galleryDrop.addEventListener('drop', e => {
    e.preventDefault(); galleryDrop.classList.remove('drag-over');
    const dt = new DataTransfer();
    Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/')).forEach(f => dt.items.add(f));
    if (dt.files.length) {
        document.getElementById('gallery-file').files = dt.files;
        addGalleryFiles(document.getElementById('gallery-file'));
    }
});
</script>
