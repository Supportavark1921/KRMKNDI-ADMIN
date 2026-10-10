@use('Illuminate\Support\Facades\Storage')
@php $isEdit = isset($product); @endphp
<style>
.ms-form-wrap{max-width:800px;margin:0 auto;padding-bottom:40px}
.ms-section{padding:24px 28px;border:1px solid #ede8e0;border-radius:16px;background:#fff;margin-bottom:16px;box-shadow:0 4px 12px #6b3a0806}
.ms-sec-title{font-size:13px;font-weight:800;color:#7a6050;letter-spacing:.06em;text-transform:uppercase;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f5ece0}
.ms-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#4a3020}
.ms-input,.ms-textarea,.ms-select{width:100%;padding:11px 13px;border:1px solid #e8ddd0;border-radius:10px;font:inherit;color:#2a1810;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.ms-input:focus,.ms-textarea:focus,.ms-select:focus{border-color:#e8813a;box-shadow:0 0 0 3px #fff4eb}
.ms-textarea{resize:vertical;min-height:80px}
.ms-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.ms-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
.ms-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}
.img-grid{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:12px}
.img-thumb{position:relative;width:90px;height:90px;border-radius:10px;overflow:hidden;border:1px solid #ede8e0}
.img-thumb img{width:100%;height:100%;object-fit:cover}
.img-thumb-rm{position:absolute;top:3px;right:3px;display:grid;width:18px;height:18px;place-items:center;border-radius:50%;background:#1b2240bb;color:#fff;font-size:9px;cursor:pointer;border:0}
.attr-row{display:flex;gap:10px;margin-bottom:8px;align-items:center}
.attr-row .ms-input{flex:1}
.btn-rm-attr{display:grid;width:30px;height:38px;place-items:center;border-radius:8px;border:1px solid #fdc9c9;background:#fff0f0;color:#d94040;cursor:pointer;font-size:13px;flex-shrink:0}
.btn-add-attr{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;border:1px dashed #e8813a;background:#fffaf5;color:#7a3a10;font:700 13px inherit;cursor:pointer;margin-top:6px}
.ms-actions{display:flex;gap:12px;justify-content:flex-end;padding:20px 0 0}
.ms-btn-primary{padding:12px 24px;border-radius:11px;border:0;color:#fff;background:linear-gradient(100deg,#c4601a,#e8813a);font:700 14px inherit;cursor:pointer;transition:.2s}
.ms-btn-primary:hover{transform:translateY(-1px)}
.ms-btn-back{padding:12px 16px;border-radius:11px;border:1px solid #ede8e0;color:#7a6050;background:#fff;font:600 14px inherit;text-decoration:none;display:inline-flex;align-items:center;transition:.15s}
.ms-btn-back:hover{background:#fffaf5}
</style>
<div class="ms-form-wrap">
    @if($errors->any())
        <div style="padding:13px 16px;border-radius:12px;background:#fff0f0;border:1px solid #fdc9c9;margin-bottom:16px;color:#b33;font-size:13px">
            <strong>Please fix:</strong>
            <ul style="margin:8px 0 0;padding-left:20px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if($isEdit)<input type="hidden" name="_method" value="PUT">@endif

        {{-- ① Basic Info --}}
        <div class="ms-section">
            <div class="ms-sec-title">① Product Info</div>
            <div style="margin-bottom:14px">
                <label class="ms-label">Product Name <span style="color:#e04a4a">*</span></label>
                <input type="text" name="name" class="ms-input" value="{{ old('name', $product->name ?? '') }}" required placeholder="e.g. Brass Diya Set">
                @error('name')<span class="ms-error">{{ $message }}</span>@enderror
            </div>
            <div class="ms-grid-2" style="margin-bottom:14px">
                <div>
                    <label class="ms-label">Category <span style="color:#e04a4a">*</span></label>
                    <select name="category_id" class="ms-select" required>
                        <option value="">— Select category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="ms-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="ms-label">SKU</label>
                    <input type="text" name="sku" class="ms-input" value="{{ old('sku', $product->sku ?? '') }}" placeholder="Unique code">
                    @error('sku')<span class="ms-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div style="margin-bottom:14px">
                <label class="ms-label">Short Description</label>
                <input type="text" name="short_description" class="ms-input" value="{{ old('short_description', $product->short_description ?? '') }}" placeholder="One-line summary">
            </div>
            <div>
                <label class="ms-label">Full Description</label>
                <textarea name="description" class="ms-textarea" rows="4" placeholder="Detailed product description…">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>

        {{-- ② Pricing --}}
        <div class="ms-section">
            <div class="ms-sec-title">② Pricing</div>
            <div class="ms-grid-2">
                <div>
                    <label class="ms-label">Price (₹) <span style="color:#e04a4a">*</span></label>
                    <input type="number" name="price" class="ms-input" step="0.01" min="0"
                           value="{{ old('price', $product->price ?? '') }}" required placeholder="0.00">
                    @error('price')<span class="ms-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="ms-label">Compare-at Price (₹)</label>
                    <input type="number" name="compare_at_price" class="ms-input" step="0.01" min="0"
                           value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}" placeholder="Original / strikethrough price">
                </div>
            </div>
        </div>

        {{-- ③ Images --}}
        <div class="ms-section">
            <div class="ms-sec-title">③ Images</div>
            @if($isEdit && $product->images->isNotEmpty())
                <div class="img-grid" id="img-grid">
                    @foreach($product->images as $img)
                        <div class="img-thumb" id="img-{{ $img->id }}">
                            <img src="{{ Storage::disk('public')->url($img->path) }}" alt="">
                            <button type="button" class="img-thumb-rm" onclick="deleteImg({{ $img->id }})">×</button>
                            <input type="hidden" name="delete_images[]" id="del-{{ $img->id }}" disabled>
                        </div>
                    @endforeach
                </div>
            @endif
            <div>
                <label class="ms-label" style="margin-bottom:8px">{{ $isEdit ? 'Add more images' : 'Upload images' }}</label>
                <input type="file" name="images[]" accept="image/*" multiple>
                <div style="color:#9a8070;font-size:11px;margin-top:5px">JPG, PNG · max 5 MB each</div>
            </div>
        </div>

        {{-- ④ Attributes --}}
        <div class="ms-section">
            <div class="ms-sec-title">④ Attributes <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:12px">(optional — e.g. Colour: Gold)</span></div>
            <div id="attr-list">
                @php $attrs = $product->attributes ?? collect(); @endphp
                @foreach($attrs as $i => $attr)
                <div class="attr-row" id="ar-{{ $i }}">
                    <input type="text" name="attr_key[]" class="ms-input" value="{{ $attr->key }}" placeholder="Key (e.g. Colour)">
                    <input type="text" name="attr_value[]" class="ms-input" value="{{ $attr->value }}" placeholder="Value (e.g. Gold)">
                    <button type="button" class="btn-rm-attr" onclick="rmAttr({{ $i }})">✕</button>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn-add-attr" onclick="addAttr()">＋ Add Attribute</button>
        </div>

        {{-- ⑤ Status --}}
        <div class="ms-section">
            <div class="ms-sec-title">⑤ Status</div>
            <select name="status" class="ms-select" style="width:auto;min-width:180px">
                @foreach(['draft' => 'Draft','active' => 'Active','inactive' => 'Inactive','sold_out' => 'Sold Out'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $product->status ?? 'draft') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="ms-actions">
            <a href="{{ route('my.store.products.index') }}" class="ms-btn-back">← Back</a>
            <button type="submit" class="ms-btn-primary">{{ $isEdit ? '💾 Save Changes' : '＋ Create Product' }}</button>
        </div>
    </form>
</div>
<script>
let ai = {{ $isEdit ? count($product->attributes ?? []) : 0 }};
function addAttr(){
    const d=document.createElement('div');d.className='attr-row';d.id='ar-'+ai;
    d.innerHTML=`<input type="text" name="attr_key[]" class="ms-input" placeholder="Key (e.g. Colour)">
        <input type="text" name="attr_value[]" class="ms-input" placeholder="Value (e.g. Gold)">
        <button type="button" class="btn-rm-attr" onclick="rmAttr(${ai})">✕</button>`;
    document.getElementById('attr-list').appendChild(d);ai++;
}
function rmAttr(i){const el=document.getElementById('ar-'+i);if(el)el.remove();}
function deleteImg(id){
    const inp=document.getElementById('del-'+id);
    if(inp){inp.disabled=false;}
    const el=document.getElementById('img-'+id);
    if(el)el.style.opacity='0.3';
}
</script>
