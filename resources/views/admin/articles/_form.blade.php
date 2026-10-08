{{-- Shared form partial: included by create.blade.php and edit.blade.php --}}
{{-- Expects: $categories, $statuses. For edit also: $article --}}
@php $editing = isset($article); @endphp

<div class="art-form-card">

    {{-- Core content --}}
    <div class="art-form-section">
        <p class="art-section-title">Content</p>
        <div class="art-grid">
            <div class="art-field full">
                <label class="art-label">Title (English) *</label>
                <input type="text" name="title" id="titleInput"
                    value="{{ old('title', $editing ? $article->title : '') }}"
                    class="art-input @error('title') is-invalid @enderror"
                    required placeholder="e.g. The Significance of Navratri" autocomplete="off">
                @error('title')<p class="art-error">{{ $message }}</p>@enderror
            </div>

            <div class="art-field full">
                <label class="art-label">Slug <span class="art-hint">(auto-generated from title if blank; lowercase letters, numbers, hyphens only)</span></label>
                <input type="text" name="slug" id="slugInput"
                    value="{{ old('slug', $editing ? $article->slug : '') }}"
                    class="art-input @error('slug') is-invalid @enderror"
                    placeholder="e.g. significance-of-navratri">
                @error('slug')<p class="art-error">{{ $message }}</p>@enderror
            </div>

            <div class="art-field full">
                <label class="art-label">Excerpt <span class="art-hint">(short summary shown in listings, max 500 chars)</span></label>
                <textarea name="excerpt" rows="2" class="art-input" placeholder="A short description shown in article listings…">{{ old('excerpt', $editing ? $article->excerpt : '') }}</textarea>
                @error('excerpt')<p class="art-error">{{ $message }}</p>@enderror
            </div>

            <div class="art-field full">
                <label class="art-label">Content *</label>
                <textarea name="content" rows="14" class="art-input @error('content') is-invalid @enderror"
                    placeholder="Full article body…" required>{{ old('content', $editing ? $article->content : '') }}</textarea>
                @error('content')<p class="art-error">{{ $message }}</p>@enderror
            </div>

            <div class="art-field full">
                <label class="art-label">Cover image <span class="art-hint">(jpg / png / webp, max 2 MB)</span></label>
                @if($editing && $article->cover_image)
                <img src="{{ Storage::url($article->cover_image) }}" class="art-img-preview" id="coverImgPreview">
                @else
                <img id="coverImgPreview" src="" class="art-img-preview" style="display:none">
                @endif
                <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="art-input" id="coverImgInput">
                @error('cover_image')<p class="art-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- Hindi translation --}}
    <div class="art-form-section">
        <p class="art-section-title">Hindi Translation <span style="font-weight:500;text-transform:none;letter-spacing:0">(optional)</span></p>
        <div class="art-grid">
            <div class="art-field full">
                <label class="art-label">Title (Hindi)</label>
                <input type="text" name="translations[hi][title]"
                    value="{{ old('translations.hi.title', $editing ? ($article->translations['hi']['title'] ?? '') : '') }}"
                    class="art-input" placeholder="हिन्दी शीर्षक">
            </div>
            <div class="art-field full">
                <label class="art-label">Excerpt (Hindi)</label>
                <textarea name="translations[hi][excerpt]" rows="2" class="art-input" placeholder="हिन्दी सारांश">{{ old('translations.hi.excerpt', $editing ? ($article->translations['hi']['excerpt'] ?? '') : '') }}</textarea>
            </div>
            <div class="art-field full">
                <label class="art-label">Content (Hindi)</label>
                <textarea name="translations[hi][content]" rows="8" class="art-input" placeholder="हिन्दी लेख सामग्री…">{{ old('translations.hi.content', $editing ? ($article->translations['hi']['content'] ?? '') : '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Settings --}}
    <div class="art-form-section">
        <p class="art-section-title">Settings</p>
        <div class="art-grid">
            <div class="art-field">
                <label class="art-label">Category *</label>
                <select name="category" class="art-input">
                    @foreach($categories as $c)
                    <option value="{{ $c }}" @selected(old('category', $editing ? $article->category : 'general') === $c)>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="art-field">
                <label class="art-label">Status *</label>
                <select name="status" class="art-input">
                    @foreach($statuses as $s)
                    <option value="{{ $s }}" @selected(old('status', $editing ? $article->status : 'draft') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="art-field">
                <label class="art-label">Tags <span class="art-hint">(comma-separated)</span></label>
                <input type="text" name="tags"
                    value="{{ old('tags', $editing && $article->tags ? implode(', ', $article->tags) : '') }}"
                    class="art-input" placeholder="e.g. navratri, puja, astrology">
            </div>
            <div class="art-field">
                <label class="art-label">Publish date <span class="art-hint">(leave blank to use current time on publish)</span></label>
                <input type="datetime-local" name="published_at"
                    value="{{ old('published_at', $editing && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}"
                    class="art-input">
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    const titleInput = document.getElementById('titleInput');
    const slugInput  = document.getElementById('slugInput');
    let slugManual   = {{ $editing ? 'true' : 'false' }};

    titleInput?.addEventListener('input', function () {
        if (slugManual) return;
        slugInput.value = this.value.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .trim().replace(/\s+/g, '-');
    });
    slugInput?.addEventListener('input', function () {
        slugManual = this.value.length > 0;
    });
})();
</script>
