@use('Illuminate\Support\Facades\Storage')
@php $isEdit = isset($guru); @endphp
<style>
.gf-wrap{max-width:860px;margin:0 auto}
.gf-section{padding:26px 30px;border:1px solid #ede0d0;border-radius:16px;background:#fff;margin-bottom:16px;box-shadow:0 4px 12px #7a3a1006}
.gf-title{display:flex;align-items:center;gap:10px;margin:0 0 20px;font-size:15px;font-weight:800;color:#2a1810}
.gf-title span{display:grid;width:28px;height:28px;place-items:center;border-radius:8px;background:#fff4eb;color:#e8813a;font-size:13px}
.gf-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#4a3020}
.gf-input,.gf-select,.gf-textarea{width:100%;padding:11px 13px;border:1px solid #e8ddd0;border-radius:10px;font:inherit;color:#2a1810;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.gf-input:focus,.gf-select:focus,.gf-textarea:focus{border-color:#e8813a;box-shadow:0 0 0 3px #fff4eb}
.gf-textarea{resize:vertical;min-height:90px}
.gf-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.gf-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}
.gf-help{display:block;margin-top:5px;color:#9a8070;font-size:11px}
.cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-top:4px}
.cat-check{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;border:1px solid #ede0d0;cursor:pointer;transition:.15s;user-select:none}
.cat-check:hover{border-color:#e8813a;background:#fffaf5}
.cat-check input{width:16px;height:16px;accent-color:#e8813a;cursor:pointer}
.cat-check span{font-size:13px;font-weight:600;color:#4a3020}
.cat-check.checked{border-color:#e8813a;background:#fff4eb}
.img-drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;border:2px dashed #e8c8a8;border-radius:14px;padding:24px;background:#fffaf5;cursor:pointer;transition:.2s;text-align:center}
.img-drop:hover,.img-drop.drag-over{border-color:#e8813a;background:#fff4eb}
.img-preview{width:120px;height:120px;border-radius:50%;overflow:hidden;border:3px solid #f0d0a8;display:none;position:relative}
.img-preview img{width:100%;height:100%;object-fit:cover}
.img-preview-rm{position:absolute;top:4px;right:4px;display:grid;width:22px;height:22px;place-items:center;border-radius:50%;background:#1b2240bb;color:#fff;cursor:pointer;font-size:11px;border:0}
.gf-toggle-wrap{display:flex;align-items:center;gap:12px}
.gf-toggle{position:relative;width:44px;height:24px;flex-shrink:0}
.gf-toggle input{position:absolute;opacity:0;width:0;height:0}
.gf-toggle-track{position:absolute;inset:0;border-radius:12px;background:#d0c8b8;transition:.2s;cursor:pointer}
.gf-toggle-track::after{position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;content:"";transition:.2s;box-shadow:0 1px 3px #0002}
.gf-toggle input:checked+.gf-toggle-track{background:#e8813a}
.gf-toggle input:checked+.gf-toggle-track::after{transform:translateX(20px)}
.gf-toggle-label{font-size:14px;font-weight:700;color:#4a3020;cursor:pointer}
.gf-actions{display:flex;gap:12px;justify-content:flex-end;padding:20px 0 0}
.gf-btn-primary{padding:12px 26px;border-radius:11px;border:0;color:#fff;background:linear-gradient(100deg,#c4601a,#e8813a);font:700 14px inherit;cursor:pointer;box-shadow:0 8px 18px #e8813a25;transition:.2s}
.gf-btn-primary:hover{transform:translateY(-1px);box-shadow:0 12px 22px #e8813a35}
.gf-btn-secondary{padding:12px 18px;border-radius:11px;border:1px solid #ede0d0;color:#7a6050;background:#fff;font:600 14px inherit;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;transition:.15s}
.gf-btn-secondary:hover{background:#fffaf5}
@media(max-width:600px){.gf-row{grid-template-columns:1fr}.cat-grid{grid-template-columns:1fr}}
</style>

<div class="gf-wrap">
    @if($errors->any())
        <div style="padding:14px 16px;border-radius:12px;background:#fff0f0;border:1px solid #fdc9c9;margin-bottom:16px;color:#b33;font-size:13px">
            <strong>Please fix:</strong>
            <ul style="margin:8px 0 0;padding-left:20px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if(isset($method))<input type="hidden" name="_method" value="{{ $method }}">@endif

        {{-- Basic info --}}
        <div class="gf-section">
            <h2 class="gf-title"><span>🕉</span> Guruji Details</h2>
            <div style="margin-bottom:16px">
                <label class="gf-label">Name <span style="color:#e04a4a">*</span></label>
                <input type="text" name="name" class="gf-input" value="{{ old('name', $guru->name ?? '') }}" required placeholder="e.g. Shri XYZ Maharaj">
                @error('name')<span class="gf-error">{{ $message }}</span>@enderror
            </div>
            <div style="margin-bottom:16px">
                <label class="gf-label">Description</label>
                <textarea name="description" class="gf-textarea" rows="3" placeholder="Brief introduction…">{{ old('description', $guru->description ?? '') }}</textarea>
            </div>
            @isset($gurujiUsers)
            <div>
                <label class="gf-label">Linked Admin Account</label>
                <select name="user_id" class="gf-select">
                    <option value="">— None —</option>
                    @foreach($gurujiUsers as $u)
                        <option value="{{ $u->id }}" {{ old('user_id', $guru->user_id ?? '') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
                <span class="gf-help">Link to the guruji's admin login so they can see their appointments.</span>
                @error('user_id')<span class="gf-error">{{ $message }}</span>@enderror
            </div>
            @endisset
        </div>

        {{-- Photo --}}
        <div class="gf-section">
            <h2 class="gf-title"><span>📷</span> Profile Photo</h2>
            <div style="display:flex;align-items:flex-start;gap:24px">
                <div id="guru-preview" class="img-preview" style="{{ $isEdit && $guru->image ? 'display:block' : '' }}">
                    <img src="{{ $isEdit && $guru->image ? Storage::disk('public')->url($guru->image) : '' }}" id="guru-preview-img" alt="">
                    <button type="button" class="img-preview-rm" onclick="removeImg()">×</button>
                </div>
                <div class="img-drop" id="guru-drop" onclick="document.getElementById('guru-img').click()" style="{{ $isEdit && $guru->image ? 'display:none' : '' }}">
                    <span style="font-size:28px;color:#d0a080">📷</span>
                    <p style="margin:0;color:#9a8070;font-size:13px">Click or drag photo here</p>
                    <small style="color:#c0a888;font-size:11px">JPG, PNG · max 5 MB · recommended: square</small>
                </div>
                <input type="file" id="guru-img" name="image" accept="image/*" style="display:none" onchange="previewImg(this)">
            </div>
        </div>

        {{-- Donation categories --}}
        <div class="gf-section">
            <h2 class="gf-title"><span>❧</span> Donation Categories</h2>
            <p style="color:#9a8070;font-size:13px;margin:0 0 14px">Select which donation purposes this Guruji accepts.</p>
            <div class="cat-grid">
                @foreach($categories as $cat)
                    <label class="cat-check {{ in_array($cat->id, $assigned ?? []) ? 'checked' : '' }}" id="cat-lbl-{{ $cat->id }}">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                               {{ in_array($cat->id, $assigned ?? []) ? 'checked' : '' }}
                               onchange="toggleCat({{ $cat->id }}, this.checked)">
                        <span>{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
            @if($categories->isEmpty())
                <p style="color:#c0a888;font-size:13px;margin:0">No active categories yet. <a href="{{ route('donation-categories.create') }}" style="color:#e8813a;font-weight:700">Create one first</a>.</p>
            @endif
        </div>

        {{-- Status --}}
        <div class="gf-section">
            <h2 class="gf-title"><span>⚙</span> Status</h2>
            <div class="gf-toggle-wrap">
                <label class="gf-toggle">
                    <input type="checkbox" id="status-toggle"
                           {{ old('status', $guru->status ?? 'active') === 'active' ? 'checked' : '' }}
                           onchange="document.getElementById('status-field').value=this.checked?'active':'inactive'">
                    <span class="gf-toggle-track"></span>
                </label>
                <input type="hidden" name="status" id="status-field" value="{{ old('status', $guru->status ?? 'active') }}">
                <label for="status-toggle" class="gf-toggle-label">Guruji is Active</label>
            </div>
            <span class="gf-help">Inactive Gurujis are hidden from the mobile app.</span>
        </div>

        <div class="gf-actions">
            <a href="{{ route('gurus.index') }}" class="gf-btn-secondary">Cancel</a>
            <button type="submit" class="gf-btn-primary">{{ $isEdit ? '💾 Save Changes' : '＋ Add Guruji' }}</button>
        </div>
    </form>
</div>
<script>
function previewImg(input) {
    if (!input.files[0]) return;
    document.getElementById('guru-preview-img').src = URL.createObjectURL(input.files[0]);
    document.getElementById('guru-preview').style.display = 'block';
    document.getElementById('guru-drop').style.display = 'none';
}
function removeImg() {
    document.getElementById('guru-preview').style.display = 'none';
    document.getElementById('guru-drop').style.display = 'flex';
    document.getElementById('guru-img').value = '';
}
function toggleCat(id, checked) {
    document.getElementById('cat-lbl-' + id).classList.toggle('checked', checked);
}
const drop = document.getElementById('guru-drop');
drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('drag-over'); });
drop.addEventListener('dragleave', () => drop.classList.remove('drag-over'));
drop.addEventListener('drop', e => {
    e.preventDefault(); drop.classList.remove('drag-over');
    const f = e.dataTransfer.files[0];
    if (f && f.type.startsWith('image/')) {
        const dt = new DataTransfer(); dt.items.add(f);
        document.getElementById('guru-img').files = dt.files;
        previewImg(document.getElementById('guru-img'));
    }
});
</script>
