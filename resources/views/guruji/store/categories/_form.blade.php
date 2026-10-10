@use('Illuminate\Support\Facades\Storage')
@php $isEdit = isset($category); @endphp
<style>
.ms-form-wrap{max-width:680px;margin:0 auto;padding-bottom:40px}
.ms-section{padding:24px 28px;border:1px solid #ede8e0;border-radius:16px;background:#fff;margin-bottom:16px;box-shadow:0 4px 12px #6b3a0806}
.ms-sec-title{font-size:13px;font-weight:800;color:#7a6050;letter-spacing:.06em;text-transform:uppercase;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f5ece0}
.ms-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#4a3020}
.ms-input,.ms-textarea,.ms-select{width:100%;padding:11px 13px;border:1px solid #e8ddd0;border-radius:10px;font:inherit;color:#2a1810;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.ms-input:focus,.ms-textarea:focus,.ms-select:focus{border-color:#e8813a;box-shadow:0 0 0 3px #fff4eb}
.ms-textarea{resize:vertical;min-height:80px}
.ms-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}
.img-drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border:2px dashed #e8c8a8;border-radius:12px;padding:18px;background:#fffaf5;cursor:pointer;transition:.2s;text-align:center}
.img-drop:hover{border-color:#e8813a;background:#fff4eb}
.img-preview-sq{width:100px;height:100px;border-radius:12px;overflow:hidden;border:2px solid #f0d0a8;display:none;position:relative}
.img-preview-sq img{width:100%;height:100%;object-fit:cover}
.img-preview-rm{position:absolute;top:4px;right:4px;display:grid;width:20px;height:20px;place-items:center;border-radius:50%;background:#1b2240bb;color:#fff;cursor:pointer;font-size:10px;border:0}
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

        <div class="ms-section">
            <div class="ms-sec-title">① Category Details</div>
            <div style="margin-bottom:14px">
                <label class="ms-label">Name <span style="color:#e04a4a">*</span></label>
                <input type="text" name="name" class="ms-input" value="{{ old('name', $category->name ?? '') }}" required placeholder="e.g. Puja Items">
                @error('name')<span class="ms-error">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="ms-label">Description</label>
                <textarea name="description" class="ms-textarea" rows="3" placeholder="Short description…">{{ old('description', $category->description ?? '') }}</textarea>
            </div>
        </div>

        <div class="ms-section">
            <div class="ms-sec-title">② Image</div>
            <div style="display:flex;align-items:flex-start;gap:20px">
                <div id="img-preview" class="img-preview-sq" style="{{ $isEdit && ($category->image ?? null) ? 'display:block' : '' }}">
                    <img src="{{ $isEdit && ($category->image ?? null) ? Storage::disk('public')->url($category->image) : '' }}" id="img-preview-img" alt="">
                    <button type="button" class="img-preview-rm" onclick="removeImg()">×</button>
                </div>
                <div class="img-drop" id="img-drop" onclick="document.getElementById('img-file').click()" style="{{ $isEdit && ($category->image ?? null) ? 'display:none' : '' }}">
                    <span style="font-size:24px;color:#e8813a">🖼</span>
                    <p style="margin:0;color:#9a8070;font-size:13px">Click to upload image</p>
                    <small style="color:#c0a888;font-size:11px">JPG, PNG · max 5 MB</small>
                </div>
                <input type="file" id="img-file" name="image" accept="image/*" style="display:none" onchange="previewImg(this)">
            </div>
            @error('image')<span class="ms-error" style="display:block;margin-top:8px">{{ $message }}</span>@enderror
        </div>

        <div class="ms-section">
            <div class="ms-sec-title">③ Status</div>
            <select name="status" class="ms-select" style="width:auto;min-width:160px">
                <option value="active" {{ old('status', $category->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $category->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="ms-actions">
            <a href="{{ route('my.store.categories.index') }}" class="ms-btn-back">← Back</a>
            <button type="submit" class="ms-btn-primary">{{ $isEdit ? '💾 Save Changes' : '＋ Create Category' }}</button>
        </div>
    </form>
</div>
<script>
function previewImg(input){if(!input.files[0])return;document.getElementById('img-preview-img').src=URL.createObjectURL(input.files[0]);document.getElementById('img-preview').style.display='block';document.getElementById('img-drop').style.display='none';}
function removeImg(){document.getElementById('img-preview').style.display='none';document.getElementById('img-drop').style.display='flex';document.getElementById('img-file').value='';}
</script>
