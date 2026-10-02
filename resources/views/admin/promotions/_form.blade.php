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

        <div class="form-group" id="cta_value_group">
            <label class="form-label">CTA value <small style="color:#888">(product ID / URL etc.)</small></label>
            <input type="text" name="cta_value" value="{{ old('cta_value', $promotion->cta_value ?? '') }}" class="form-input" placeholder="e.g. 42 or https://…">
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
document.getElementById('cta_type')?.addEventListener('change', function () {
    document.getElementById('cta_value_group').style.display = this.value === 'none' ? 'none' : '';
});
document.addEventListener('DOMContentLoaded', function () {
    const ct = document.getElementById('cta_type');
    if (ct) document.getElementById('cta_value_group').style.display = ct.value === 'none' ? 'none' : '';
});
</script>
