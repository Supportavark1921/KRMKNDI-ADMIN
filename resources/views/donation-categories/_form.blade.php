@use('Illuminate\Support\Facades\Storage')
@php $isEdit = isset($category); @endphp
<style>
.gf-wrap{max-width:760px;margin:0 auto}
.gf-section{padding:24px 28px;border:1px solid #ede0d0;border-radius:16px;background:#fff;margin-bottom:16px;box-shadow:0 4px 12px #7a3a1006}
.gf-title{display:flex;align-items:center;gap:10px;margin:0 0 18px;font-size:15px;font-weight:800;color:#2a1810}
.gf-title span{display:grid;width:28px;height:28px;place-items:center;border-radius:8px;background:#fff4eb;color:#e8813a;font-size:13px}
.gf-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#4a3020}
.gf-input,.gf-select,.gf-textarea{width:100%;padding:11px 13px;border:1px solid #e8ddd0;border-radius:10px;font:inherit;color:#2a1810;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.gf-input:focus,.gf-select:focus,.gf-textarea:focus{border-color:#e8813a;box-shadow:0 0 0 3px #fff4eb}
.gf-textarea{resize:vertical;min-height:80px}
.gf-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}
.guru-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;margin-top:4px}
.guru-check{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;border:1px solid #ede0d0;cursor:pointer;transition:.15s;user-select:none}
.guru-check:hover,.guru-check.checked{border-color:#e8813a;background:#fff4eb}
.guru-check input{width:15px;height:15px;accent-color:#e8813a;cursor:pointer}
.guru-check span{font-size:13px;font-weight:600;color:#4a3020}
.img-drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border:2px dashed #e8c8a8;border-radius:12px;padding:20px;background:#fffaf5;cursor:pointer;transition:.2s;text-align:center}
.img-drop:hover{border-color:#e8813a;background:#fff4eb}
.img-preview-sq{width:100px;height:100px;border-radius:12px;overflow:hidden;border:2px solid #f0d0a8;display:none;position:relative}
.img-preview-sq img{width:100%;height:100%;object-fit:cover}
.img-preview-rm{position:absolute;top:4px;right:4px;display:grid;width:20px;height:20px;place-items:center;border-radius:50%;background:#1b2240bb;color:#fff;cursor:pointer;font-size:10px;border:0}
.gf-toggle-wrap{display:flex;align-items:center;gap:12px}
.gf-toggle{position:relative;width:44px;height:24px;flex-shrink:0}
.gf-toggle input{position:absolute;opacity:0;width:0;height:0}
.gf-toggle-track{position:absolute;inset:0;border-radius:12px;background:#d0c8b8;transition:.2s;cursor:pointer}
.gf-toggle-track::after{position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;content:"";transition:.2s;box-shadow:0 1px 3px #0002}
.gf-toggle input:checked+.gf-toggle-track{background:#e8813a}
.gf-toggle input:checked+.gf-toggle-track::after{transform:translateX(20px)}
.gf-actions{display:flex;gap:12px;justify-content:flex-end;padding:20px 0 0}
.gf-btn-primary{padding:12px 24px;border-radius:11px;border:0;color:#fff;background:linear-gradient(100deg,#c4601a,#e8813a);font:700 14px inherit;cursor:pointer;box-shadow:0 8px 18px #e8813a25;transition:.2s}
.gf-btn-primary:hover{transform:translateY(-1px)}
.gf-btn-secondary{padding:12px 16px;border-radius:11px;border:1px solid #ede0d0;color:#7a6050;background:#fff;font:600 14px inherit;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;transition:.15s}
.gf-btn-secondary:hover{background:#fffaf5}
</style>
<div class="gf-wrap">
    @if($errors->any())
        <div style="padding:13px 16px;border-radius:12px;background:#fff0f0;border:1px solid #fdc9c9;margin-bottom:16px;color:#b33;font-size:13px">
            <strong>Please fix:</strong>
            <ul style="margin:8px 0 0;padding-left:20px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if(isset($method))<input type="hidden" name="_method" value="{{ $method }}">@endif

        <div class="gf-section">
            <h2 class="gf-title"><span>❧</span> Category Details</h2>
            <div style="margin-bottom:14px">
                <label class="gf-label">Category Name <span style="color:#e04a4a">*</span></label>
                <input type="text" name="name" class="gf-input" value="{{ old('name', $category->name ?? '') }}" required placeholder="e.g. Ann Prasadhan">
                @error('name')<span class="gf-error">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="gf-label">Description</label>
                <textarea name="description" class="gf-textarea" rows="3" placeholder="What this donation supports…">{{ old('description', $category->description ?? '') }}</textarea>
            </div>
        </div>

        <div class="gf-section">
            <h2 class="gf-title"><span>🖼</span> Category Image / Icon</h2>
            <div style="display:flex;align-items:flex-start;gap:20px">
                <div id="cat-preview" class="img-preview-sq" style="{{ $isEdit && $category->image ? 'display:block' : '' }}">
                    <img src="{{ $isEdit && $category->image ? Storage::disk('public')->url($category->image) : '' }}" id="cat-preview-img" alt="">
                    <button type="button" class="img-preview-rm" onclick="removeCatImg()">×</button>
                </div>
                <div class="img-drop" id="cat-drop" onclick="document.getElementById('cat-img').click()" style="{{ $isEdit && $category->image ? 'display:none' : '' }}">
                    <span style="font-size:24px;color:#d0a080">🖼</span>
                    <p style="margin:0;color:#9a8070;font-size:13px">Click or drag image here</p>
                    <small style="color:#c0a888;font-size:11px">JPG, PNG · max 5 MB</small>
                </div>
                <input type="file" id="cat-img" name="image" accept="image/*" style="display:none" onchange="previewCatImg(this)">
            </div>
        </div>

        <div class="gf-section">
            <h2 class="gf-title"><span>🕉</span> Assign to Gurujis</h2>
            <p style="color:#9a8070;font-size:13px;margin:0 0 12px">This category will appear as a donation purpose for selected Gurujis.</p>
            <div class="guru-grid">
                @foreach($gurus as $guru)
                    <label class="guru-check {{ in_array($guru->id, $assigned ?? []) ? 'checked' : '' }}" id="guru-lbl-{{ $guru->id }}">
                        <input type="checkbox" name="gurus[]" value="{{ $guru->id }}"
                               {{ in_array($guru->id, $assigned ?? []) ? 'checked' : '' }}
                               onchange="this.closest('label').classList.toggle('checked', this.checked)">
                        <span>{{ $guru->name }}</span>
                    </label>
                @endforeach
            </div>
            @if($gurus->isEmpty())
                <p style="color:#c0a888;font-size:13px;margin:0">No active Gurujis yet. <a href="{{ route('gurus.create') }}" style="color:#e8813a;font-weight:700">Create one first</a>.</p>
            @endif
        </div>

        <div class="gf-section">
            <h2 class="gf-title"><span>⚙</span> Status</h2>
            <div class="gf-toggle-wrap">
                <label class="gf-toggle">
                    <input type="checkbox" id="st"
                           {{ old('status', $category->status ?? 'active') === 'active' ? 'checked' : '' }}
                           onchange="document.getElementById('sf').value=this.checked?'active':'inactive'">
                    <span class="gf-toggle-track"></span>
                </label>
                <input type="hidden" name="status" id="sf" value="{{ old('status', $category->status ?? 'active') }}">
                <label for="st" style="font-size:14px;font-weight:700;color:#4a3020;cursor:pointer">Category is Active</label>
            </div>
            <span style="display:block;margin-top:5px;color:#9a8070;font-size:11px">Inactive categories are hidden from the mobile app. Historical donations are preserved.</span>
        </div>

        <div class="gf-actions">
            <a href="{{ route('donation-categories.index') }}" class="gf-btn-secondary">Cancel</a>
            <button type="submit" class="gf-btn-primary">{{ $isEdit ? '💾 Save Changes' : '＋ Create Category' }}</button>
        </div>
    </form>
</div>
<script>
function previewCatImg(input){if(!input.files[0])return;document.getElementById('cat-preview-img').src=URL.createObjectURL(input.files[0]);document.getElementById('cat-preview').style.display='block';document.getElementById('cat-drop').style.display='none';}
function removeCatImg(){document.getElementById('cat-preview').style.display='none';document.getElementById('cat-drop').style.display='flex';document.getElementById('cat-img').value='';}
</script>
