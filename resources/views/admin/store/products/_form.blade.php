<style>
.store-card{background:#fff;border:1px solid #e6e8f0;border-radius:16px;padding:24px;margin-bottom:18px}
.store-card h3{margin:0 0 18px;font-size:14px;font-weight:800;color:#15233d}
.fg label{display:block;font-size:11px;font-weight:700;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.fg input,.fg select,.fg textarea{width:100%;padding:10px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:14px;color:#15233d;background:#fafbfc;font-family:inherit}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#6246ea;outline:none;box-shadow:0 0 0 3px #6246ea18}
.fg-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.fg-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px}
.btn-run{background:#6246ea;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:800;cursor:pointer;padding:11px 20px}
.attr-row{display:grid;grid-template-columns:1fr 1fr 32px;gap:8px;align-items:center;margin-bottom:8px}
.attr-row button{width:32px;height:32px;border:1px solid #dde1ef;border-radius:7px;background:#fff;cursor:pointer;color:#c0392b;font-size:16px}
.toggle-row{display:flex;align-items:center;gap:10px;padding:8px 0}
.toggle-row label{margin:0;font-size:14px;color:#15233d;font-weight:600;cursor:pointer}
</style>

<div class="store-card">
    <h3>Basic Info</h3>
    <div class="fg" style="margin-bottom:14px">
        <label>Product Name *</label>
        <input name="name" value="{{ old('name', $product->name ?? '') }}" required>
    </div>
    <div class="fg-grid">
        <div class="fg">
            <label>Category *</label>
            <select name="category_id" required>
                <option value="">— Select —</option>
                @foreach($categories as $parent)
                    @if($parent->children->isNotEmpty())
                        <optgroup label="{{ $parent->name }}">
                            @foreach($parent->children as $sub)
                                <option value="{{ $sub->id }}" @selected(old('category_id', $product->category_id ?? '') == $sub->id)>{{ $sub->name }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $parent->id }}" @selected(old('category_id', $product->category_id ?? '') == $parent->id)>{{ $parent->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="fg">
            <label>Mataji <small style="text-transform:none;font-size:10px">(optional)</small></label>
            <select name="mataji_id">
                <option value="">— None / All —</option>
                @foreach($matajis as $m)
                    <option value="{{ $m->id }}" @selected(old('mataji_id', $product->mataji_id ?? '') == $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="fg-grid">
        <div class="fg">
            <label>Product Type *</label>
            <select name="product_type" required>
                <option value="NORMAL" @selected(old('product_type', $product->product_type ?? 'NORMAL') === 'NORMAL')>Normal Product</option>
                <option value="MATAJI_OFFERING" @selected(old('product_type', $product->product_type ?? '') === 'MATAJI_OFFERING')>Mataji Offering</option>
                <option value="MATAJI_OFFERED_RESALE" @selected(old('product_type', $product->product_type ?? '') === 'MATAJI_OFFERED_RESALE')>Offered Resale</option>
            </select>
        </div>
        <div class="fg">
            <label>Status *</label>
            <select name="status" required>
                <option value="draft" @selected(old('status', $product->status ?? 'draft') === 'draft')>Draft</option>
                <option value="active" @selected(old('status', $product->status ?? '') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $product->status ?? '') === 'inactive')>Inactive</option>
                <option value="sold_out" @selected(old('status', $product->status ?? '') === 'sold_out')>Sold Out</option>
            </select>
        </div>
    </div>
    <div class="fg" style="margin-bottom:14px">
        <label>Short Description</label>
        <input name="short_description" value="{{ old('short_description', $product->short_description ?? '') }}" placeholder="One-liner shown in listing">
    </div>
    <div class="fg">
        <label>Full Description</label>
        <textarea name="description" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
</div>

<div class="store-card">
    <h3>Pricing & SKU</h3>
    <div class="fg-grid-3">
        <div class="fg">
            <label>Price (₹) *</label>
            <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" required>
        </div>
        <div class="fg">
            <label>Compare-at Price (₹)</label>
            <input name="compare_at_price" type="number" step="0.01" min="0" value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}">
        </div>
        <div class="fg">
            <label>SKU</label>
            <input name="sku" value="{{ old('sku', $product->sku ?? '') }}">
        </div>
    </div>
    <div class="fg">
        <label>Brand / Source</label>
        <input name="brand_source" value="{{ old('brand_source', $product->brand_source ?? '') }}" placeholder="e.g. Surat Weaves">
    </div>
</div>

<div class="store-card">
    <h3>Flags</h3>
    <div class="toggle-row">
        <input type="hidden" name="offering_eligible" value="0">
        <input type="checkbox" id="offering_eligible" name="offering_eligible" value="1"
            {{ old('offering_eligible', $product->offering_eligible ?? false) ? 'checked' : '' }}
            style="width:16px;height:16px">
        <label for="offering_eligible">Offering Eligible — can be bought as Mataji offering</label>
    </div>
    <div class="toggle-row">
        <input type="hidden" name="resale_eligible" value="0">
        <input type="checkbox" id="resale_eligible" name="resale_eligible" value="1"
            {{ old('resale_eligible', $product->resale_eligible ?? false) ? 'checked' : '' }}
            style="width:16px;height:16px">
        <label for="resale_eligible">Resale Eligible — can be listed as resale after offering</label>
    </div>
</div>

<div class="store-card">
    <h3>Attributes <small style="font-size:11px;font-weight:400;color:#8a9ab8">(fabric, color, occasion, size…)</small></h3>
    <div id="attr-list">
        @php $existingAttrs = old('attr_key') ? array_map(null, old('attr_key', []), old('attr_value', [])) : ($product->attributes ?? collect())->map(fn($a) => [$a->key, $a->value])->toArray() @endphp
        @foreach($existingAttrs as [$k, $v])
        <div class="attr-row">
            <input name="attr_key[]" value="{{ $k }}" placeholder="e.g. color">
            <input name="attr_value[]" value="{{ $v }}" placeholder="e.g. Red">
            <button type="button" onclick="this.closest('.attr-row').remove()" title="Remove">×</button>
        </div>
        @endforeach
    </div>
    <button type="button" onclick="addAttr()" style="margin-top:4px;padding:7px 14px;border:1px dashed #8a9ab8;border-radius:8px;background:#fff;cursor:pointer;color:#536078;font-size:12px;font-weight:700">+ Add Attribute</button>
</div>

@isset($product)
@else
<div class="store-card">
    <h3>Initial Stock</h3>
    <div class="fg" style="max-width:200px">
        <label>Quantity</label>
        <input name="initial_stock" type="number" min="0" value="0" placeholder="0">
    </div>
</div>
@endisset

<div class="store-card">
    <h3>Images</h3>
    @if(isset($product) && $product->images->count())
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px">
            @foreach($product->images as $img)
            <div style="position:relative">
                <img src="{{ $img->url() }}" style="width:72px;height:72px;border-radius:10px;object-fit:cover;border:{{ $img->is_primary ? '2px solid #6246ea' : '1px solid #dde1ef' }}">
                <form method="POST" action="{{ route('admin.store.products.images.delete', $img) }}" style="position:absolute;top:-5px;right:-5px" onsubmit="return confirm('Remove image?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="width:18px;height:18px;border-radius:50%;background:#c0392b;color:#fff;border:none;cursor:pointer;font-size:11px;line-height:1">×</button>
                </form>
                @unless($img->is_primary)
                <form method="POST" action="{{ route('admin.store.products.images.primary', $img) }}" style="margin-top:4px">
                    @csrf
                    <button type="submit" style="width:72px;padding:3px;border:1px solid #dde1ef;border-radius:5px;background:#fff;font-size:10px;cursor:pointer;color:#536078">Set primary</button>
                </form>
                @endunless
            </div>
            @endforeach
        </div>
    @endif
    <input type="file" name="images[]" accept="image/*" multiple style="font-size:13px">
    <div style="margin-top:6px;font-size:11px;color:#8a9ab8">First image becomes primary on create. JPG/PNG, max 5 MB each.</div>
</div>

<script>
function addAttr() {
    const row = document.createElement('div');
    row.className = 'attr-row';
    row.innerHTML = '<input name="attr_key[]" placeholder="e.g. color"><input name="attr_value[]" placeholder="e.g. Red"><button type="button" onclick="this.closest(\'.attr-row\').remove()" title="Remove">×</button>';
    document.getElementById('attr-list').appendChild(row);
    row.querySelector('input').focus();
}
</script>
