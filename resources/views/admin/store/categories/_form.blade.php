<style>
.store-card{background:#fff;border:1px solid #e6e8f0;border-radius:16px;padding:24px;margin-bottom:18px}
.store-card h3{margin:0 0 18px;font-size:14px;font-weight:800;color:#15233d}
.fg label{display:block;font-size:11px;font-weight:700;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.fg input,.fg select,.fg textarea{width:100%;padding:10px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:14px;color:#15233d;background:#fafbfc;font-family:inherit}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#2d6a4f;outline:none;box-shadow:0 0 0 3px #2d6a4f18}
.fg-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.btn-run{background:#2d6a4f;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:800;cursor:pointer;padding:11px 20px}
</style>

<div class="store-card">
    <h3>Category Details</h3>

    {{-- Parent category (makes this a subcategory) --}}
    <div class="fg" style="margin-bottom:14px">
        <label>Parent Category <small style="text-transform:none;font-size:10px">(leave blank to make a top-level category)</small></label>
        <select name="parent_id">
            <option value="">— Top-level category —</option>
            @foreach($parents as $p)
                <option value="{{ $p->id }}" @selected(old('parent_id', $category->parent_id ?? null) == $p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="fg-grid">
        <div class="fg">
            <label>Name *</label>
            <input name="name" value="{{ old('name', $category->name ?? '') }}" required>
        </div>
        <div class="fg">
            <label>Slug <small style="text-transform:none;font-size:10px">(auto-generated if blank)</small></label>
            <input name="slug" value="{{ old('slug', $category->slug ?? '') }}" placeholder="e.g. silk-sarees">
        </div>
    </div>
    <div class="fg" style="margin-bottom:14px">
        <label>Description</label>
        <textarea name="description" rows="2">{{ old('description', $category->description ?? '') }}</textarea>
    </div>
    <div class="fg-grid">
        <div class="fg">
            <label>Sort Order</label>
            <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
        </div>
        <div class="fg">
            <label>Status</label>
            <select name="status">
                <option value="active"   @selected(old('status', $category->status ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $category->status ?? '') === 'inactive')>Inactive</option>
            </select>
        </div>
    </div>
</div>

<div class="store-card">
    <h3>Image</h3>
    @if(isset($category) && $category->image)
        <img src="{{ Storage::url($category->image) }}" style="width:70px;height:70px;border-radius:10px;object-fit:cover;margin-bottom:10px;display:block">
    @endif
    <input type="file" name="image" accept="image/*" style="font-size:13px">
</div>
